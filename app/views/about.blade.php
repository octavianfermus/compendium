@extends('layout')

@section('content')
    <div class="container application">
        <div class="row">
            <div class="col-md-12 main">
                <div class="boxWrapper heading">
                    <h1>Algorithm Compendium Thesis</h1>
                    <p class="subtitle">Summary</p>
                    <p><span>Proposed by </span>Vasile-Octavian Fermuș</p>
                    <p><span>Scientific coordinator: </span>Asist. Dr. Vasile Alaiba</p>
                    <p><span>Link to documentation: </span><a href="{{ URL::to('algorithm_compendium.pdf') }}">Click here for a PDF of the documentation.</a></p>
                </div>
                <div class="boxWrapper text-justify">
                    <p style="text-indent: 50px;">Through this application I propose a system for collecting algorithm that would be useful to programmers in any field of IT that search for solutions to problems that exist in personal applications, representing an alternative to search engines.</p>
                    <p style="text-indent: 50px;">At the present time, there are several platforms where users can find help to their problems but none of them treat the issue the way I plan to. Safe to mention are platforms as “W3Schools”, a platform that comprises a high array of guides for web development and tutorials, but without user input, or “Stack Overflow”, a platform that tries to help users by letting them ask questions regarding various topics while other users answer, but without great built-in support (at least in my opinion).</p>
                    <p style="text-indent: 50px;">What I plan on doing is creating a blend between the group and private messaging functions of Facebook, the comment system of blog services, the ask/respond model of platforms like Stack Overflow and the possibility of commenting certain parts of content, as seen on web applications such as Soundcloud.</p>
                </div>
            </div>
        </div>
    </div>
@stop