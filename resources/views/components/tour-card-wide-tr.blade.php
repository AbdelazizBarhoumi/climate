@props(['tour', 'searchedTag' => null])

<x-wide-tour-card-main 
    :tour="$tour" 
    :searchedTag="$searchedTag" 
    showTransition="true" 
    containerClasses="h-32" 
    tagsClasses="z-10"
/>