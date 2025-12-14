<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->table }}
        
        <x-filament::section>
            <x-slot name="heading">
                CSV Format Guide
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Questions Guide -->
                <div class="prose dark:prose-invert max-w-none">
                    <h3 class="text-lg font-bold text-primary-600 dark:text-primary-400 mb-4 border-b pb-2">
                        1. Questions File Guide
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        The Questions CSV file must have <strong>7 columns</strong> in the following order:
                    </p>
                    <ol class="text-sm text-gray-600 dark:text-gray-400 list-decimal ml-5 space-y-1">
                        <li><strong>Question Number:</strong> Integer (e.g., 1)</li>
                        <li><strong>Question Text:</strong> Full text of the question</li>
                        <li><strong>Option 1:</strong> Text of the first option</li>
                        <li><strong>Option 2:</strong> Text of the second option</li>
                        <li><strong>Option 3:</strong> Text of the third option</li>
                        <li><strong>Option 4:</strong> Text of the fourth option</li>
                        <li><strong>Correct Option Number:</strong> Number between 1 and 4</li>
                    </ol>
                    
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                        <p class="text-sm font-bold text-blue-800 dark:text-blue-200 mb-2">Example Questions File:</p>
                        <code class="text-xs text-blue-700 dark:text-blue-300 block whitespace-pre font-mono bg-white dark:bg-black/20 p-2 rounded dir-ltr text-left">1,"What is the capital of France?","Paris","London","Berlin","Madrid",1
2,"What does HTML stand for?","Hyper Text Markup Language","High Text Markup Language","Hyper Tabular Markup Language","None of these",1</code>
                    </div>
                </div>

                <!-- Explanations Guide -->
                <div class="prose dark:prose-invert max-w-none">
                    <h3 class="text-lg font-bold text-success-600 dark:text-success-400 mb-4 border-b pb-2">
                        2. Explanations File Guide
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        The Explanations CSV file must have <strong>2 columns</strong> in the following order:
                    </p>
                    <ol class="text-sm text-gray-600 dark:text-gray-400 list-decimal ml-5 space-y-1">
                        <li><strong>Question Number:</strong> Number of the question the explanation belongs to</li>
                        <li><strong>Explanation Text:</strong> Full text of the explanation</li>
                    </ol>
                    
                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800">
                        <p class="text-sm font-bold text-green-800 dark:text-green-200 mb-2">Example Explanations File:</p>
                        <code class="text-xs text-green-700 dark:text-green-300 block whitespace-pre font-mono bg-white dark:bg-black/20 p-2 rounded dir-ltr text-left">1,"Paris is the capital and most populous city of France."
2,"HTML stands for Hyper Text Markup Language."</code>
                    </div>

                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800">
                        <p class="text-sm font-bold text-amber-800 dark:text-amber-200 mb-1">Important Note:</p>
                        <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                            The system finds the explanation based on the <strong>"Question Number"</strong> in the selected exam.
                            <br>
                            For example, if you write <code>5,"Explanation..."</code> in the explanations file, the system looks for a question in the current exam with number 5 and saves the explanation for it.
                        </p>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
