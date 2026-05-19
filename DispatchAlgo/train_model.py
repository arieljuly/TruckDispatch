# train_model.py
import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, r2_score
import joblib
import os

print("=" * 60)
print("Training Fuel Prediction Model from CSV Datasets")
print("=" * 60)

# Define paths to your CSV files
DATASET_PATH = 'Datasets'

# Load all files
print("\n📂 Loading CSV files from:", DATASET_PATH)

trips = pd.read_csv(f'{DATASET_PATH}/trips.csv')
events = pd.read_csv(f'{DATASET_PATH}/delivery_events.csv')
facilities = pd.read_csv(f'{DATASET_PATH}/facilities.csv')
routes = pd.read_csv(f'{DATASET_PATH}/routes.csv')
trucks = pd.read_csv(f'{DATASET_PATH}/trucks.csv')

print(f"✅ Loaded trips: {len(trips)} records")
print(f"✅ Loaded delivery_events: {len(events)} records")
print(f"✅ Loaded facilities: {len(facilities)} records")
print(f"✅ Loaded routes: {len(routes)} records")
print(f"✅ Loaded trucks: {len(trucks)} records")

# Load logistics if exists
logistics = None
if os.path.exists(f'{DATASET_PATH}/logistics.csv'):
    logistics = pd.read_csv(f'{DATASET_PATH}/logistics.csv')
    print(f"✅ Loaded logistics: {len(logistics)} records")

# Process events summary
event_summary = events.groupby('trip_id').agg({
    'detention_minutes': 'sum',
    'on_time_flag': 'min'
}).reset_index()

# Merge trips with events
df = trips.merge(event_summary, on='trip_id', how='left')

# Convert units
df['distance_km'] = df['actual_distance_miles'] * 1.60934
df['fuel_used_liters'] = df['fuel_gallons_used'] * 3.78541
df['delay_minutes'] = df['detention_minutes']

# Merge with trucks to get truck efficiency (if available)
if 'truck_id' in df.columns and 'truck_id' in trucks.columns:
    # Calculate truck average efficiency from historical data
    truck_efficiency = trucks[['truck_id', 'avg_fuel_efficiency']] if 'avg_fuel_efficiency' in trucks.columns else None
    if truck_efficiency is not None:
        df = df.merge(truck_efficiency, on='truck_id', how='left')
        print("✅ Merged truck efficiency data")

# Merge with logistics if available
if logistics is not None and 'trip_id' in logistics.columns:
    df = df.merge(logistics, on='trip_id', how='left')
    print("✅ Merged with logistics data")

# Define features (EXCLUDING calculated fields to prevent data leakage)
# DO NOT use average_mpg as it's likely calculated from distance/fuel
features = [
    'distance_km',
    'actual_duration_hours',
    'idle_time_hours',
    'detention_minutes',
    'delay_minutes',
    'on_time_flag'
]

# Add truck efficiency if available
if 'avg_fuel_efficiency' in df.columns:
    features.append('avg_fuel_efficiency')
    print("✅ Using truck efficiency as a feature")

target = 'fuel_used_liters'

# Prepare the dataset
model_df = df[features + [target]].copy()

# Handle missing values
model_df = model_df.dropna()

# Convert boolean to integer
if 'on_time_flag' in model_df.columns:
    model_df['on_time_flag'] = model_df['on_time_flag'].astype(int)

print(f"\n📊 Final dataset shape: {model_df.shape}")
print(f"   Features: {features}")
print(f"   Target: {target}")

# Verify no perfect correlation
print("\n🔍 Data validation:")
print(f"   Min fuel: {model_df['fuel_used_liters'].min():.2f} L")
print(f"   Max fuel: {model_df['fuel_used_liters'].max():.2f} L")
print(f"   Avg fuel: {model_df['fuel_used_liters'].mean():.2f} L")

X = model_df[features]
y = model_df[target]

# Split data
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

print(f"\n📈 Training samples: {len(X_train)}")
print(f"   Testing samples: {len(X_test)}")

# Train Random Forest model
print("\n🎯 Training Random Forest Regressor...")
model = RandomForestRegressor(
    n_estimators=100,
    max_depth=15,
    min_samples_split=10,
    min_samples_leaf=5,
    random_state=42,
    n_jobs=-1,
    verbose=1
)

model.fit(X_train, y_train)

# Evaluate
y_pred = model.predict(X_test)
mae = mean_absolute_error(y_test, y_pred)
r2 = r2_score(y_test, y_pred)

print("\n" + "=" * 60)
print("Model Performance")
print("=" * 60)
print(f"✅ Mean Absolute Error (MAE): {mae:.2f} liters")
print(f"✅ R² Score: {r2:.4f}")

# Feature importance
print("\n📈 Feature Importance:")
importance = dict(zip(features, model.feature_importances_))
for feature, imp in sorted(importance.items(), key=lambda x: x[1], reverse=True):
    bars = "█" * int(imp * 50)
    print(f"   {feature:25s}: {imp:.4f} ({imp*100:.1f}%) {bars}")

# Save the model
model_path = 'fuel_prediction_model.pkl'
joblib.dump(model, model_path)
model_size = os.path.getsize(model_path) / 1024 / 1024

print(f"\n✅ Model saved to: {model_path}")
print(f"   Size: {model_size:.2f} MB")

# Save feature list for reference
joblib.dump(features, 'model_features.pkl')
print(f"✅ Feature list saved to model_features.pkl")

# Test predictions
print("\n" + "=" * 60)
print("Testing Model Predictions")
print("=" * 60)

test_cases = [
    ([100, 2, 0, 0, 0, 1], "Normal trip (100km, 2hrs, on-time)"),
    ([100, 2, 1, 0, 0, 1], "With 1hr idle"),
    ([100, 2, 0, 30, 0, 1], "With 30min detention"),
    ([100, 2, 0, 0, 15, 1], "With 15min delay"),
    ([100, 2, 0, 0, 0, 0], "Late delivery"),
]

for test_input, description in test_cases:
    prediction = model.predict([test_input])[0]
    print(f"\n🧪 {description}")
    print(f"   → Predicted fuel: {prediction:.2f} liters")

print("\n" + "=" * 60)
print("✅ Training complete! Model is ready for use.")
print("=" * 60)