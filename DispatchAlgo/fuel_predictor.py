import pandas as pd
import numpy as np
from typing import Dict, Tuple, Optional, List
import joblib
import os
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class FuelPredictor:
    """Machine Learning model for fuel consumption prediction using existing datasets"""
    
    def __init__(self, datasets_path: str = "Datasets", model_path: str = "fuel_model.pkl"):
        self.datasets_path = datasets_path
        self.model_path = model_path
        self.model = None
        self.label_encoders = {}
        self.features = [
            'distance_km',
            'actual_duration_hours',
            'average_mpg',
            'idle_time_hours',
            'detention_minutes',
            'delay_minutes',
            'on_time_flag'
        ]
        self._load_model()
    
    def _load_model(self):
        """Load trained model if exists"""
        if os.path.exists(self.model_path):
            try:
                self.model = joblib.load(self.model_path)
                logger.info(f"✅ Loaded fuel prediction model from {self.model_path}")
            except Exception as e:
                logger.warning(f"Could not load model: {e}")
                self.model = None
        else:
            logger.info("No existing model found. Train one using: python train_model.py")
    
    def load_and_prepare_data(self) -> pd.DataFrame:
        """Load and prepare data from CSV files in Datasets folder"""
        
        logger.info(f"Loading datasets from {self.datasets_path}")
        
        # Define file paths
        trips_path = os.path.join(self.datasets_path, 'trips.csv')
        events_path = os.path.join(self.datasets_path, 'delivery_events.csv')
        trucks_path = os.path.join(self.datasets_path, 'trucks.csv')
        routes_path = os.path.join(self.datasets_path, 'routes.csv')
        facilities_path = os.path.join(self.datasets_path, 'facilities.csv')
        
        # Check if files exist
        if not os.path.exists(trips_path):
            logger.warning(f"Trips file not found: {trips_path}")
            return self._create_sample_data()
        
        # Load data
        trips = pd.read_csv(trips_path)
        logger.info(f"Loaded trips: {len(trips)} records")
        
        # Load events if exists
        if os.path.exists(events_path):
            events = pd.read_csv(events_path)
            logger.info(f"Loaded delivery_events: {len(events)} records")
            
            # Aggregate events by trip
            event_summary = events.groupby('trip_id').agg({
                'detention_minutes': 'sum',
                'on_time_flag': 'min'
            }).reset_index()
            
            # Merge trips with events
            df = trips.merge(event_summary, on='trip_id', how='left')
        else:
            df = trips.copy()
        
        # Convert units
        if 'actual_distance_miles' in df.columns:
            df['distance_km'] = df['actual_distance_miles'] * 1.60934
        elif 'distance_km' not in df.columns:
            df['distance_km'] = df.get('distance', 100)
        
        if 'fuel_gallons_used' in df.columns:
            df['fuel_used_liters'] = df['fuel_gallons_used'] * 3.78541
        elif 'fuel_used_liters' not in df.columns:
            df['fuel_used_liters'] = df['distance_km'] / 6.0  # Default efficiency
        
        # Fill missing values
        df['actual_duration_hours'] = df.get('actual_duration_hours', df['distance_km'] / 50)
        df['idle_time_hours'] = df.get('idle_time_hours', 0)
        df['detention_minutes'] = df.get('detention_minutes', 0)
        df['delay_minutes'] = df.get('delay_minutes', 0)
        df['on_time_flag'] = df.get('on_time_flag', 1).fillna(1).astype(int)
        df['average_mpg'] = df.get('average_mpg', 6.0)
        
        # Load truck efficiency if available
        if os.path.exists(trucks_path):
            trucks = pd.read_csv(trucks_path)
            if 'truck_id' in df.columns and 'truck_id' in trucks.columns:
                if 'avg_fuel_efficiency' in trucks.columns:
                    df = df.merge(trucks[['truck_id', 'avg_fuel_efficiency']], on='truck_id', how='left')
                    df['average_mpg'] = df['avg_fuel_efficiency'].fillna(df['average_mpg'])
        
        # Drop rows with missing target
        df = df.dropna(subset=['fuel_used_liters'])
        
        logger.info(f"Prepared dataset: {len(df)} records")
        
        return df
    
    def _create_sample_data(self) -> pd.DataFrame:
        """Create sample data for training if datasets not available"""
        logger.info("Creating sample training data")
        
        np.random.seed(42)
        n_samples = 1000
        
        data = {
            'distance_km': np.random.uniform(10, 500, n_samples),
            'actual_duration_hours': np.random.uniform(0.5, 10, n_samples),
            'average_mpg': np.random.uniform(4, 12, n_samples),
            'idle_time_hours': np.random.exponential(0.5, n_samples),
            'detention_minutes': np.random.poisson(10, n_samples),
            'delay_minutes': np.random.poisson(5, n_samples),
            'on_time_flag': np.random.choice([0, 1], n_samples, p=[0.2, 0.8]),
        }
        
        # Calculate fuel used based on features
        data['fuel_used_liters'] = (
            data['distance_km'] / data['average_mpg'] +
            data['idle_time_hours'] * 2 +
            data['detention_minutes'] / 60 * 1.5 +
            data['delay_minutes'] / 60 * 2.5
        ) * np.where(data['on_time_flag'] == 1, 0.95, 1.10)
        
        # Add noise
        data['fuel_used_liters'] = data['fuel_used_liters'] * np.random.uniform(0.9, 1.1, n_samples)
        
        return pd.DataFrame(data)
    
    def train_model(self, historical_data: pd.DataFrame = None) -> Dict:
        """Train the fuel prediction model"""
        
        if historical_data is None:
            historical_data = self.load_and_prepare_data()
        
        if historical_data.empty:
            logger.error("No data available for training")
            return {"error": "No data available for training"}
        
        # Prepare features
        available_features = [f for f in self.features if f in historical_data.columns]
        
        if not available_features:
            logger.error("No valid features found")
            return {"error": "No valid features found"}
        
        X = historical_data[available_features]
        y = historical_data['fuel_used_liters']
        
        # Handle any remaining missing values
        X = X.fillna(0)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42
        )
        
        # Train model
        self.model = RandomForestRegressor(
            n_estimators=100,
            max_depth=15,
            min_samples_split=10,
            min_samples_leaf=5,
            random_state=42,
            n_jobs=-1
        )
        self.model.fit(X_train, y_train)
        
        # Evaluate
        train_score = self.model.score(X_train, y_train)
        test_score = self.model.score(X_test, y_test)
        
        # Calculate feature importance
        feature_importance = dict(zip(available_features, self.model.feature_importances_))
        
        # Save model
        joblib.dump(self.model, self.model_path)
        
        # Save features list
        joblib.dump(available_features, 'model_features.pkl')
        
        logger.info(f"Model trained - Train R²: {train_score:.4f}, Test R²: {test_score:.4f}")
        
        return {
            "train_r2": train_score,
            "test_r2": test_score,
            "feature_importance": feature_importance,
            "features_used": available_features,
            "training_samples": len(X_train),
            "test_samples": len(X_test)
        }
    
    def predict_fuel(
        self,
        distance_km: float,
        actual_duration_hours: float,
        average_mpg: float = 6.0,
        idle_time_hours: float = 0,
        detention_minutes: int = 0,
        delay_minutes: int = 0,
        on_time_flag: bool = True
    ) -> Tuple[float, float, float, float]:
        """
        Predict fuel consumption for a trip
        
        Returns:
            (predicted_fuel_liters, confidence_score, lower_bound, upper_bound)
        """
        
        if self.model is not None:
            try:
                # Prepare features
                features = pd.DataFrame([[
                    distance_km,
                    actual_duration_hours,
                    average_mpg,
                    idle_time_hours,
                    detention_minutes,
                    delay_minutes,
                    1 if on_time_flag else 0
                ]], columns=self.features)
                
                predicted_fuel = float(self.model.predict(features)[0])
                confidence = 0.92
                
                # Calculate prediction interval (95% confidence)
                std_dev = 0.08 * predicted_fuel
                lower_bound = predicted_fuel - 1.96 * std_dev
                upper_bound = predicted_fuel + 1.96 * std_dev
                
            except Exception as e:
                logger.warning(f"ML prediction failed: {e}, using fallback")
                return self._fallback_prediction(distance_km, average_mpg, idle_time_hours, 
                                                  detention_minutes, delay_minutes, on_time_flag)
        else:
            # Use fallback calculation
            return self._fallback_prediction(distance_km, average_mpg, idle_time_hours,
                                              detention_minutes, delay_minutes, on_time_flag)
        
        return predicted_fuel, confidence, max(0, lower_bound), upper_bound
    
    def _fallback_prediction(self, distance_km: float, average_mpg: float = 6.0,
                             idle_time_hours: float = 0, detention_minutes: int = 0,
                             delay_minutes: int = 0, on_time_flag: bool = True) -> Tuple[float, float, float, float]:
        """Fallback calculation when ML model is unavailable"""
        
        # Driving fuel consumption
        if average_mpg > 0:
            driving_fuel = distance_km / average_mpg
        else:
            driving_fuel = distance_km / 6.0
        
        # Idle time fuel consumption (2 liters per hour)
        idle_fuel = idle_time_hours * 2.0
        
        # Detention fuel (1.5 liters per hour)
        detention_fuel = (detention_minutes / 60) * 1.5
        
        # Delay fuel (2.5 liters per hour)
        delay_fuel = (delay_minutes / 60) * 2.5
        
        # On-time adjustment
        if on_time_flag:
            on_time_adjustment = 0.95
        else:
            on_time_adjustment = 1.10
        
        # Calculate total
        total_fuel = (driving_fuel + idle_fuel + detention_fuel + delay_fuel) * on_time_adjustment
        
        # Add 10% safety margin
        predicted_fuel = total_fuel * 1.1
        
        confidence = 0.75
        
        # Prediction interval (85% confidence for fallback)
        std_dev = 0.15 * predicted_fuel
        lower_bound = predicted_fuel - 1.44 * std_dev
        upper_bound = predicted_fuel + 1.44 * std_dev
        
        return predicted_fuel, confidence, max(0, lower_bound), upper_bound
    
    def get_feature_importance(self) -> Dict:
        """Get feature importance from trained model"""
        if self.model and hasattr(self.model, 'feature_importances_'):
            importance = dict(zip(self.features, self.model.feature_importances_))
            return {k: round(v * 100, 1) for k, v in sorted(importance.items(), key=lambda x: x[1], reverse=True)}
        return {}
    
    def batch_predict(self, trips_data: pd.DataFrame) -> pd.DataFrame:
        """Make predictions for multiple trips"""
        predictions = []
        
        for _, row in trips_data.iterrows():
            pred, conf, lower, upper = self.predict_fuel(
                distance_km=row.get('distance_km', 100),
                actual_duration_hours=row.get('actual_duration_hours', 2),
                average_mpg=row.get('average_mpg', 6.0),
                idle_time_hours=row.get('idle_time_hours', 0),
                detention_minutes=row.get('detention_minutes', 0),
                delay_minutes=row.get('delay_minutes', 0),
                on_time_flag=row.get('on_time_flag', True)
            )
            predictions.append({
                'predicted_fuel_liters': pred,
                'confidence_score': conf,
                'prediction_interval_lower': lower,
                'prediction_interval_upper': upper
            })
        
        return pd.DataFrame(predictions)