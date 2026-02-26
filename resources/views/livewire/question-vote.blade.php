<div class="flex items-center gap-4 mt-4 text-sm text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100 w-max">
    <span class="font-medium text-gray-700">Did you like this question?</span>
    
    <button 
        wire:click="vote(1)" 
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors duration-200 {{ $userVote === 1 ? 'bg-green-100 text-green-700 font-medium' : 'hover:bg-green-50 text-gray-500 hover:text-green-600' }}"
        title="Like"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $userVote === 1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z" />
        </svg>
        <span>{{ $likesCount }}</span>
    </button>

    <div class="w-px h-5 bg-gray-300"></div>

    <button 
        wire:click="vote(-1)" 
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors duration-200 {{ $userVote === -1 ? 'bg-red-100 text-red-700 font-medium' : 'hover:bg-red-50 text-gray-500 hover:text-red-600' }}"
        title="Dislike"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $userVote === -1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13.5H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
        </svg>
        <!-- Let's use thumbs down icon instead of standard dislike button -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $userVote === -1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 01-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 011.423.23l3.114 1.04a4.5 4.5 0 001.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 008.25 19.5a2.25 2.25 0 002.25 2.25.75.75 0 00.75-.75v-1.5c0-.766-.234-1.488-.636-2.09A9.04 9.04 0 019.25 15.25v-1.5M7.498 15.25A4.498 4.498 0 0010.5 11.25M17.25 15.25h2.25m-2.25 0h-2.25m-3.75 0h.008v.008h-.008v-.008zm4.5 0h.008v.008h-.008v-.008zm4.5 0h.008v.008h-.008v-.008z" />
        </svg>
        <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $userVote === -1 ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 13.5H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 01-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 011.423.23l3.114 1.04a4.5 4.5 0 001.423.23h1.294M7.5 13.5c.618 0 .991.724.725 1.282A7.471 7.471 0 008.25 19.5a2.25 2.25 0 002.25 2.25.75.75 0 00.75-.75v-1.5c0-.766.23-1.488.632-2.091A9.035 9.035 0 019.25 13.5v-1.5m0 0a4.498 4.498 0 013-3.953" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.367 13.5c-.806 0-1.533.446-2.031 1.08a9.041 9.041 0 01-2.861 2.4c-.723.384-1.35.956-1.653 1.715a4.498 4.498 0 00-.322 1.672V21a.75.75 0 00.75.75A2.25 2.25 0 0013.5 19.5c0-1.152.26-2.243.723-3.218.266-.558-.107-1.282-.725-1.282H10.372c-1.026 0-1.945-.694-2.054-1.715-.045-.422-.068-.85-.068-1.285a11.95 11.95 0 012.649-7.521c.388-.482.987-.729 1.605-.729H10.52c.483 0 .964.078 1.423.23l3.114 1.04a4.501 4.501 0 001.423.23h1.616M9.75 15h-2.25M18.096 5.25c-.083-.205-.173-.405-.27-.602-.197-.4.078-.898.523-.898h.908c.889 0 1.713.518 1.972 1.368.175.568.353 1.153.521 3.507 0 1.553-.295 3.036-.831 4.398C20.613 13.797 19.833 14.25 19 14.25h-1.053c-.472 0-.745-.556-.5-.96a8.958 8.958 0 001.302-4.665c0-1.194-.232-2.333-.654-3.375z" />
        </svg>
        <span>{{ $dislikesCount }}</span>
    </button>
</div>
