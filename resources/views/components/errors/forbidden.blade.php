@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
    <div id="app">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">

                        </ol>
                    </div>
                    <h4 class="page-title"></h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center">
                    <h1 class="text-error mt-4">403</h1>
                    <h4 class="text-uppercase text-danger mt-3">Acceso Prohibido</h4>
                    <p class="text-muted mt-3">
                        Si crees que esto es un error, por favor, verifica tus credenciales de acceso o ponte en
                        contacto con el
                        administrador del sitio.
                        <br><br>
                        Recuerda que incluso los mejores navegantes se equivocan a veces. ¡Ánimo y sigue explorando! 😊
                    </p>

                    <a class="btn btn-info mt-3" href="/dashboard"><i class="mdi mdi-reply"></i> Regresar a casa</a>
                </div> <!-- end /.text-center-->
            </div> <!-- end col-->
        </div>
        <!-- end row -->
    </div>
@endsection
@section('scripts')
@endsection
