@extends('layouts.app')

@section('title', 'Gestión avanzada de usuarios')

@section('styles')

<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-perfil.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-gestion.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

@endsection

@section('content')
    @include('layouts.mensajes')
    <div class="contenedor-gestor">
        {{-- CABECERA --}}
        <div class="cabecera-gestor">
            <h2 class="titulo-gestor">
                Gestión avanzada de usuarios
            </h2>
            <a href="{{ route('admin_index') }}" class="boton-volver-gestor">
                Volver al panel
            </a>
        </div>

        {{--BUSCADOR--}}
        <form method="GET" action="{{ route('admin_buscar_usuarios') }}" class="buscador-gestor">
            <input  type="text" name="buscar" placeholder="Buscar por nombre de usuario..." value="{{ request('buscar') }}" class="input-busqueda-gestor">
            <button type="submit" class="gestor-btn-azul">
                Buscar
            </button>
        </form>
        {{--DATA-TABLE --}}
        <div class="contenedor-tabla-gestor">
            <table id="tablaUsuarios" class="tabla-gestor">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Rol</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Último login</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ ucfirst($usuario->role) }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->nombre_usuario }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if($usuario->bloqueado)
                                    <span class="estado-bloqueado">Bloqueado</span>
                                @else
                                    <span class="estado-activo">Activo</span>
                                @endif
                            </td>
                            <td>{{ $usuario->ultimo_login }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="color:#ff4d6d;">
                                No se encontraron usuarios
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection


@section('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
    //JS para el data table
    $(document).ready(function () {
        $('#tablaUsuarios').DataTable({
            //Paginación
            pageLength: 20,
            //traducción de todas las partes del data table
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
                infoEmpty: "No hay usuarios disponibles",
                infoFiltered: "(filtrado de _MAX_ usuarios totales)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "No hay datos en la tabla",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            },
            //para quitar el buscador js y hacerlo desde el backend por mi
            dom: '<"barra-gestor"B>rtip',
            //Botón para descargar la tabla en csv o excel
            buttons: [
                {
                    extend: 'csvHtml5',
                    text: 'Descargar EXCEL',
                    className: 'boton-csv-gestor',
                    title: 'usuarios_sistema'
                }
            ]
        });
    });
</script>
@endsection