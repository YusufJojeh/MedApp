#!/usr/bin/env python3

# Test the current server.py code
import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

# Import the predict_intent function
from nlp.server import predict_intent

# Test the function
test_text = "Book an appointment"
result = predict_intent(test_text)

print("Testing predict_intent function:")
print(f"Input: '{test_text}'")
print(f"Intent: {result['intent']}")
print(f"Confidence: {result['confidence']}")
print(f"Specialty Hint: {result['specialty_hint']}")

# Test other variations
test_cases = [
    "Schedule an appointment",
    "Make an appointment",
    "Book with a doctor",
    "Find a doctor",
    "I need health advice"
]

print("\nTesting other cases:")
for test_case in test_cases:
    result = predict_intent(test_case)
    print(f"'{test_case}' -> {result['intent']} (confidence: {result['confidence']})")
