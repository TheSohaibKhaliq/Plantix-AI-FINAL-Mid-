#!/usr/bin/env python3
import sys
import traceback
from flask import Flask

try:
    # Try importing the app
    from app import app
    
    # Try creating a test client
    client = app.test_client()
    
    # Try accessing the home route
    response = client.get('/')
    
    print(f"Status Code: {response.status_code}")
    print(f"Response Data (first 500 chars): {response.data[:500]}")
    
except Exception as e:
    print(f"Error occurred: {str(e)}")
    print("\n" + "="*50)
    print("Full traceback:")
    print("="*50)
    traceback.print_exc()
