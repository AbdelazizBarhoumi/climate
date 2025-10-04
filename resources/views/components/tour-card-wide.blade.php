@props(['tour', 'searchedTag' => null])

<x-wide-tour-card-main
    :tour="$tour" 
    :searchedTag="$searchedTag" 
    contentClasses="group-hover:text-blue-800 transition-colors duration-300" 
    linkEmployerName="true"
    containerClasses="h-32" 
    panelClasses='flex gap-x-6  hover:border-blue-800 transition-colors duration-300'


/>