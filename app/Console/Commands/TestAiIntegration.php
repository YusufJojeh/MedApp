<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AiAssistantService;

class TestAiIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test {--message= : Test message to send to AI}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test AI integration with Flask backend';

    protected $aiService;

    /**
     * Create a new command instance.
     */
    public function __construct(AiAssistantService $aiService)
    {
        parent::__construct();
        $this->aiService = $aiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Testing AI Integration...');
        $this->newLine();

        // Test 1: Health Check
        $this->info('1. Testing AI Service Health...');
        $health = $this->aiService->checkHealth();

        if ($health['success']) {
            $this->info('✅ AI Service is healthy');
            $this->line('Response: ' . json_encode($health['data']));
        } else {
            $this->error('❌ AI Service health check failed');
            $this->line('Error: ' . ($health['error'] ?? 'Unknown error'));
            return 1;
        }
        $this->newLine();

        // Test 2: Intent Prediction
        $this->info('2. Testing Intent Prediction...');
        $testMessage = $this->option('message') ?? 'I need to book an appointment with a cardiologist';
        $this->line("Testing with message: '{$testMessage}'");

        $intent = $this->aiService->predictIntent($testMessage);

        if ($intent['success']) {
            $this->info('✅ Intent prediction successful');
            $this->line('Intent: ' . json_encode($intent['data']));
        } else {
            $this->error('❌ Intent prediction failed');
            $this->line('Error: ' . ($intent['error'] ?? 'Unknown error'));
        }
        $this->newLine();

        // Test 3: Medical Q&A
        $this->info('3. Testing Medical Q&A...');
        $question = 'What are the symptoms of diabetes?';
        $this->line("Testing with question: '{$question}'");

        $qa = $this->aiService->answerQA($question);

        if ($qa['success']) {
            $this->info('✅ Medical Q&A successful');
            $this->line('Answer: ' . json_encode($qa['data']));
        } else {
            $this->error('❌ Medical Q&A failed');
            $this->line('Error: ' . ($qa['error'] ?? 'Unknown error'));
        }
        $this->newLine();

        // Test 4: Doctor Suggestions
        $this->info('4. Testing Doctor Suggestions...');
        $specialty = 'Cardiology';
        $this->line("Testing with specialty: '{$specialty}'");

        $doctors = $this->aiService->suggestDoctors($specialty);

        if ($doctors['success']) {
            $this->info('✅ Doctor suggestions successful');
            $this->line('Suggestions: ' . json_encode($doctors['data']));
        } else {
            $this->error('❌ Doctor suggestions failed');
            $this->line('Error: ' . ($doctors['error'] ?? 'Unknown error'));
        }
        $this->newLine();

        // Test 5: Comprehensive Text Processing
        $this->info('5. Testing Comprehensive Text Processing...');
        $text = 'I have a headache and need to see a doctor';
        $this->line("Testing with text: '{$text}'");

        $process = $this->aiService->processText($text);

        if ($process['success']) {
            $this->info('✅ Text processing successful');
            $this->line('Result: ' . json_encode($process['data'], JSON_PRETTY_PRINT));
        } else {
            $this->error('❌ Text processing failed');
            $this->line('Error: ' . ($process['error'] ?? 'Unknown error'));
        }
        $this->newLine();

        // Test 6: Voice Input Processing
        $this->info('6. Testing Voice Input Processing...');
        $transcript = 'I want to book an appointment for tomorrow';
        $this->line("Testing with transcript: '{$transcript}'");

        $voice = $this->aiService->processVoiceInput($transcript);

        if ($voice['success']) {
            $this->info('✅ Voice processing successful');
            $this->line('Voice Response: ' . ($voice['data']['voice_response'] ?? 'No response'));
        } else {
            $this->error('❌ Voice processing failed');
            $this->line('Error: ' . ($voice['error'] ?? 'Unknown error'));
        }
        $this->newLine();

        // Test 7: Local Functions
        $this->info('7. Testing Local AI Functions...');

        // Test symptom analysis
        $symptoms = 'fever and headache';
        $advice = $this->aiService->analyzeSymptoms($symptoms);
        $this->line("Symptom analysis for '{$symptoms}':");
        $this->line($advice);
        $this->newLine();

        // Test specialist suggestions
        $specialists = $this->aiService->getRelevantSpecialists($symptoms);
        $this->line("Relevant specialists for '{$symptoms}': " . implode(', ', $specialists));
        $this->newLine();

        // Test urgency assessment
        $urgency = $this->aiService->assessUrgency($symptoms, 'moderate');
        $this->line("Urgency level for '{$symptoms}': {$urgency}");
        $this->newLine();

        $this->info('🎉 AI Integration Test Complete!');

        return 0;
    }
}
