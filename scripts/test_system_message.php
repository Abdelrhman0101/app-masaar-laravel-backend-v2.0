<?php
// Quick CLI test: create a Sanctum token for admin and create a system message in the latest conversation

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function out($label, $value) {
    if (is_array($value) || is_object($value)) {
        echo $label . ': ' . json_encode($value, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo $label . ': ' . $value . "\n";
    }
}

try {
    $conn = config('database.default');
    out('DB_CONNECTION', $conn);

    // Ensure admin user exists
    $admin = User::where('email', 'admin@msar.app')->first();
    if (!$admin) {
        out('ERROR', 'Admin user admin@msar.app not found');
        exit(1);
    }
    out('AdminID', $admin->id);

    // Create token for visibility
    $token = $admin->createToken('cli-test')->plainTextToken;
    out('Token', substr($token, 0, 16) . '...');

    // Persist token to local storage for follow-up API tests
    Storage::disk('local')->put('admin_token.txt', $token);

    // Find a conversation to post into (prefer one involving admin)
    $conversation = Conversation::where('user1_id', $admin->id)
        ->orWhere('user2_id', $admin->id)
        ->orderByDesc('id')
        ->first();

    if (!$conversation) {
        // fallback to any conversation
        $conversation = Conversation::orderByDesc('id')->first();
    }

    if (!$conversation) {
        out('ERROR', 'No conversations found to send a system message to.');
        exit(1);
    }

    out('ConversationID', $conversation->id);

    // Create system message with sender_id = null
    $msg = Message::createSystemMessage(
        $conversation->id,
        'رسالة نظام اختبار عبر سكربت CLI',
        ['source' => 'cli', 'timestamp' => now()->toIso8601String()]
    );

    // Reload message with relations
    $msg->load(['sender:id,name', 'conversation:id,title']);

    $created = [
        'id' => $msg->id,
        'conversation_id' => $msg->conversation_id,
        'sender_id' => $msg->sender_id,
        'type' => $msg->type,
        'content' => $msg->content,
        'metadata' => $msg->metadata,
        'created_at' => (string)$msg->created_at,
    ];

    out('CreatedMessage', $created);

    // Validate null sender and type system
    $ok = is_null($msg->sender_id) && $msg->type === 'system';
    out('Validation', $ok ? 'OK' : 'FAILED');

    // Persist a report for later inspection and to feed next step scripts
    $report = [
        'db_connection' => $conn,
        'admin_id' => $admin->id,
        'token_prefix' => substr($token, 0, 16),
        'conversation_id' => $conversation->id,
        'created_message' => $created,
        'validation' => $ok ? 'OK' : 'FAILED',
        'generated_at' => now()->toIso8601String(),
    ];
    Storage::disk('local')->put('test_system_message.json', json_encode($report, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));

    exit($ok ? 0 : 2);

} catch (Throwable $e) {
    out('EXCEPTION', $e->getMessage());
    out('TRACE', $e->getTraceAsString());
    exit(1);
}