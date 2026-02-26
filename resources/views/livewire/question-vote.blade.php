<div class="flex items-center gap-4 mt-4 text-sm text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100 w-max mx-auto">
    <span class="font-medium text-gray-700">Rate this question:</span>
    
    <button 
        wire:click="vote(1)" 
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors duration-200 {{ $userVote === 1 ? 'bg-green-100 text-green-700 font-bold' : 'hover:bg-green-50 text-gray-500 hover:text-green-600' }}"
        title="Positive Vote"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span class="text-base">{{ $likesCount }}</span>
    </button>

    <div class="w-px h-5 bg-gray-300"></div>

    <button 
        wire:click="vote(-1)" 
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors duration-200 {{ $userVote === -1 ? 'bg-red-100 text-red-700 font-bold' : 'hover:bg-red-50 text-gray-500 hover:text-red-600' }}"
        title="Negative Vote"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
        </svg>
        <span class="text-base">{{ $dislikesCount }}</span>
    </button>
</div>
