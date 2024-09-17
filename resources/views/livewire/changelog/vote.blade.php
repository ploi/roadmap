<div class="flex flex-col rounded-lg p-3 bg-white h-fit md:w-52">
    <b class="text-md text-center">
        @if($votes->count() > 0)
            {{ trans('changelog.votes.liked-by') }}
        @else
            {{ trans('changelog.votes.no-likes') }}
        @endif
    </b>
    <div class="flex flex-col flex-wrap items-center justify-between gap-x-8 sm:w-auto space-2 p-4">
        <div class="flex -space-x-0.5">
            @if( $votes->count() > 0 )
                @php( $i = 0 )
                @foreach($votes as $vote)
                    <dd>
                        <img class="h-10 w-10 rounded-full bg-gray-50 ring-2 ring-white dark:bg-gray-950"
                             src="{{ $vote->user->getGravatar() }}" alt="{{ $vote->user->name }}">
                    </dd>
                    @php( $i++ )
                    @if( $i == 4 && $votes->count() > 5 )
                        @break
                    @elseif( $i == 5 )
                        @break
                    @endif
                @endforeach

                @if( $votes->count() > 5 )
                    <dd>
                        <div class="h-11 w-11 rounded-full bg-gray-100 ring-2 ring-white flex items-center justify-center">
                            +{{ $votes->count() - 4 }}</div>
                    </dd>
                @endif
            @else
                <dd>
                    <div class="h-11 w-11 rounded-full bg-gray-100 ring-2 ring-white flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                        </svg>
                    </div>
                </dd>
            @endif
        </div>
    </div>
    @auth
        <button wire:click="vote" type="button" class="
            inline-flex items-center justify-center gap-x-1.5 rounded-md px-2.5 py-1.5
            text-sm font-semibold text-white shadow-sm
            @if( $changelog->hasVoted( auth()->user() ) )
                bg-red-500 hover:bg-red-700
            @else
               bg-blue-500 hover:bg-blue-700
            @endif
            focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600
            ">
            @if( $changelog->hasVoted( auth()->user() ) )
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ trans('changelog.votes.remove-like') }}
            @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                </svg>
                {{ trans('changelog.votes.like') }}
            @endif
        </button>
    @endauth
    @guest
        <button type="button" class="
                inline-flex items-center justify-center gap-x-1.5 rounded-md px-2.5 py-1.5
                text-sm font-semibold text-black shadow-sm
                bg-gray-50
                cursor-not-allowed
                focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-pink-600
                ">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ trans('changelog.votes.login') }}
        </button>
    @endguest
</div>

