<x-app-layout>
    {{echo $users}}
    <h1>liste des utilisateurs</h1>
    <ul>
        <li><a href="{{ route('user.show', 1)}}">Utilisateur 1</a></li>
        <li><a href="{{ route('user.show', 1)}}">Utilisateur 2</a></li>
        <li><a href="{{ route('user.show', 1)}}">Utilisateur 3</a></li>
    </ul>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</x-app-layout>
