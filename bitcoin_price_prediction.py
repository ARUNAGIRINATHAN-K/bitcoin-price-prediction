import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

from sklearn.preprocessing import MinMaxScaler
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LinearRegression
from sklearn.ensemble import RandomForestRegressor
from sklearn.svm import SVR
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score

import xgboost as xgb
import ta

# Load the dataset
df = pd.read_csv("bitcoin.csv")

# Convert date and set index
df['Date'] = pd.to_datetime(df['Date'])
df.set_index('Date', inplace=True)

# Forward fill missing values
df.fillna(method='ffill', inplace=True)

# Normalize numerical features
scaler = MinMaxScaler()
numerical_cols = ['Open', 'High', 'Low', 'Close', 'Volume']
df[numerical_cols] = scaler.fit_transform(df[numerical_cols])

# Technical Indicators
df['SMA_10'] = df['Close'].rolling(window=10).mean()
df['EMA_10'] = df['Close'].ewm(span=10, adjust=False).mean()

# Bollinger Bands
bb = ta.volatility.BollingerBands(close=df['Close'])
df['BB_High'] = bb.bollinger_hband()
df['BB_Low'] = bb.bollinger_lband()

# RSI
df['RSI'] = ta.momentum.RSIIndicator(close=df['Close']).rsi()

# Drop rows with NaN values (caused by rolling indicators)
df.dropna(inplace=True)

# EDA: Visualize Close Price
plt.figure(figsize=(12, 6))
df['Close'].plot(title='Bitcoin Normalized Close Price Over Time')
plt.show()

# EDA: Correlation Matrix
plt.figure(figsize=(10, 6))
sns.heatmap(df.corr(), annot=True, cmap='coolwarm')
plt.title("Feature Correlation Matrix")
plt.show()

# Prepare features and target
X = df.drop('Close', axis=1)
y = df['Close']

# Split into train and test sets (80/20)
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, shuffle=False)

# Initialize models
models = {
    'Linear Regression': LinearRegression(),
    'Random Forest': RandomForestRegressor(),
    'SVM': SVR(),
    'XGBoost': xgb.XGBRegressor()
}

# Train and evaluate models
results = {}
for name, model in models.items():
    model.fit(X_train, y_train)
    preds = model.predict(X_test)
    results[name] = {
        'MAE': mean_absolute_error(y_test, preds),
        'MSE': mean_squared_error(y_test, preds),
        'RMSE': np.sqrt(mean_squared_error(y_test, preds)),
        'R2 Score': r2_score(y_test, preds)
    }
    print(f"\n{name} Evaluation:")
    print(f"  MAE:  {results[name]['MAE']:.6f}")
    print(f"  MSE:  {results[name]['MSE']:.6f}")
    print(f"  RMSE: {results[name]['RMSE']:.6f}")
    print(f"  R²:   {results[name]['R2 Score']:.6f}")

# Optional: Predict with best model
best_model = min(results, key=lambda x: results[x]['RMSE'])
print(f"\nBest model based on RMSE: {best_model}")
