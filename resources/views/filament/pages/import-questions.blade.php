<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->table }}
        
        <x-filament::section>
            <x-slot name="heading">
                CSV Format Guide
            </x-slot>
            
            <div class="grid grid-cols-1 gap-8">
                <!-- Combined Import Guide -->
                <div class="prose dark:prose-invert max-w-none">
                    <h3 class="text-lg font-bold text-primary-600 dark:text-primary-400 mb-4 border-b pb-2">
                        CSV Format Guide (Recommended)
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        The CSV file should have <strong>8 columns</strong> in the following order:
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <ol class="text-sm text-gray-600 dark:text-gray-400 list-decimal ml-5 space-y-1">
                                <li><strong>Question Number:</strong> Integer (e.g., 1)</li>
                                <li><strong>Question Text:</strong> Full text of the question</li>
                                <li><strong>Option A:</strong> Text for option A</li>
                                <li><strong>Option B:</strong> Text for option B</li>
                                <li><strong>Option C:</strong> Text for option C</li>
                                <li><strong>Option D:</strong> Text for option D</li>
                                <li><strong>Correct Answer:</strong> A, B, C, D or 1, 2, 3, 4</li>
                                <li><strong>Explanation:</strong> (Optional) Explanation text</li>
                            </ol>
                        </div>
                        <div>
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800 h-full">
                                <p class="text-sm font-bold text-blue-800 dark:text-blue-200 mb-2">Example CSV Content:</p>
                                <code class="text-xs text-blue-700 dark:text-blue-300 block whitespace-pre font-mono bg-white dark:bg-black/20 p-2 rounded dir-ltr text-left overflow-x-auto">1,"What is the most important reason for doing a vehicle inspection?","To save money on fuel","To check the radio functionality","Safety for yourself and other road users","To avoid getting a ticket","C","Safety is the most important reason..."
2,"Which of these statements about backing a heavy vehicle is true?","You should always back towards the right side","You should avoid backing whenever possible","Helpers should stand directly behind the vehicle","Backing is safer than pulling forward","B","Explanation: Because you cannot see everything..."</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
