Write-Host "========================================" -ForegroundColor Green
Write-Host "Medical Booking System - Status Check" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Check Laravel Application
Write-Host "Checking Laravel Application (Port 8000)..." -ForegroundColor Yellow
try {
    $laravelResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -UseBasicParsing -TimeoutSec 5
    Write-Host "✅ Laravel Application: RUNNING (Status: $($laravelResponse.StatusCode))" -ForegroundColor Green
} catch {
    Write-Host "❌ Laravel Application: NOT RUNNING" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Check Flask AI Service
Write-Host "Checking Flask AI Service (Port 5005)..." -ForegroundColor Yellow
try {
    $flaskResponse = Invoke-WebRequest -Uri "http://127.0.0.1:5005/health" -UseBasicParsing -TimeoutSec 5
    $flaskData = $flaskResponse.Content | ConvertFrom-Json
    Write-Host "✅ Flask AI Service: RUNNING (Status: $($flaskResponse.StatusCode))" -ForegroundColor Green
    Write-Host "   Models Loaded: $($flaskData.data_loaded.doctors)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ Flask AI Service: NOT RUNNING" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test AI Processing
Write-Host "Testing AI Processing..." -ForegroundColor Yellow
try {
    $body = @{text="I have a headache"} | ConvertTo-Json
    $aiResponse = Invoke-WebRequest -Uri "http://127.0.0.1:5005/process" -Method POST -Body $body -ContentType "application/json" -UseBasicParsing -TimeoutSec 10
    $aiData = $aiResponse.Content | ConvertFrom-Json
    Write-Host "✅ AI Processing: WORKING" -ForegroundColor Green
    Write-Host "   Intent: $($aiData.intent.intent)" -ForegroundColor Cyan
    Write-Host "   Confidence: $($aiData.intent.confidence)" -ForegroundColor Cyan
} catch {
    Write-Host "❌ AI Processing: FAILED" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Access URLs:" -ForegroundColor Yellow
Write-Host "Laravel App: http://127.0.0.1:8000" -ForegroundColor Cyan
Write-Host "AI Assistant: http://127.0.0.1:8000/ai-assistant" -ForegroundColor Cyan
Write-Host "Flask API: http://127.0.0.1:5005" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Green
