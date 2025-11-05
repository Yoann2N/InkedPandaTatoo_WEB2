<x-public-layout>


    <div style="width: 100%; text-align: center; height: 100px;">
        image banniere ici
    </div>

      <div style="width: 100%; text-align: center; height: 100px;">
        Vidéo de présentation ici
    </div>
    <h1> Welcome to the Inked Panda Tattoo Website </h1>

    <h2> Nos Artistes </h2>
    <ul>
        @foreach ($artistes as $artiste)
            <li>{{ $artiste->pseudo }} - {{ $artiste->style}}</li>
        @endforeach
    </ul>
</x-public-layout>