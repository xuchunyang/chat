<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AskKimi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Message $message
    ) {
        $key = config('services.kimi.api_key');
        if (! $key) {
            logger('Kimi API key is missing');

            return;
        }

        // Create kimi if not exists
        $kimiUser = User::where('name', 'kimi')->first() ?: User::create([
            'name' => 'kimi',
            'email' => Str::uuid().'@example.com',
            'password' => Str::uuid(),
        ]);

        logger('Asking Kimi', ['message' => $message]);

        $body = [
            'model' => 'moonshot-v1-8k',
            'messages' => [
                ['role' => 'system', 'content' => '用户将向你提问，其中 @kimi 代表你，请保持有趣'],
                ['role' => 'user', 'content' => $message->content],
            ],
            'temperature' => 0.3,
        ];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$key,
        ])->post('https://api.moonshot.cn/v1/chat/completions', $body);

        if ($response->failed()) {
            logger('Failed to ask Kimi', [
                'requestBody' => $body,
                'response' => $response->json(),
            ]);

            return;
        }
        /*
{
  "id": "chatcmpl-11b6416d3b6544c8800053072aa12611",
  "object": "chat.completion",
  "created": 15625999,
  "model": "moonshot-v1-8k",
  "choices": [
    {
      "index": 0,
      "message": {
        "role": "assistant",
        "content": "你好，李雷！1+1等于2。如果你有其他问题或需要帮助，随时告诉我！"
      },
      "finish_reason": "stop"
    }
  ],
  "usage": {
    "prompt_tokens": 81,
    "completion_tokens": 23,
    "total_tokens": 104
  }
}
         */
        // $response->json()['choices'][0]['message']['content'];
        // Create new message
        Message::create([
            'room_id' => $message->room_id,
            'user_id' => $kimiUser->id,
            'content' => $response->json()['choices'][0]['message']['content'],
        ]);
        logger('Kimi replied', [
            'requestBody' => $body,
            'response' => $response->json(),
            'message' => $message,
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
