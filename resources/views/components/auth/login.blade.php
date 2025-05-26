@extends('layouts.auth_app')

@section('title', 'Iniciar Sesión')

@section('auth-content')
    <div class="auth-fluid">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box">
            <div class="card-body d-flex flex-column gap-3">

                <!-- Logo -->
                <div class="auth-brand text-center text-lg-start">
                    <a href="index.html" class="logo-dark">
                        <span><img src="{{ asset('assets/images/lgo.png') }}" alt="dark logo" width="58%"></span>
                    </a>
                    <a href="index.html" class="logo-light">
                        <span><img src="{{ asset('assets/images/lgo.png') }}" alt="logo" width="58%"></span>
                    </a>
                </div>

                <div class="my-auto">
                    <!-- title-->
                    <h4 class="mt-0">Iniciar sesión</h4>
                    <p class="text-muted mb-2">Ingrese su dirección de correo electrónico y contraseña para acceder a la
                        cuenta.</p>

                    <!-- form -->
                    <form name="login-form" id="login-form">
                        <div class="mb-3">
                            <label for="emailaddress" class="form-label">Correo electrónico</label>
                            <input class="form-control" name="email" type="email" id="emailaddress" required=""
                                placeholder="Ingresa tu correo electrónico" value="">
                        </div>
                        <div class="mb-3">
                            <a href="pages-recoverpw-2.html" class="text-muted float-end"><small>¿Olvidaste tu
                                    contraseña?</small></a>
                            <label for="password" class="form-label">Contraseña</label>
                            <input class="form-control" name="password" type="password" required="" id="password"
                                placeholder="Ingresa tu contraseña" value="">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                <label class="form-check-label" for="checkbox-signin">Recuérdame</label>
                            </div>
                        </div>
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-login"></i> Iniciar sesión
                            </button>
                        </div>
                        <!-- social-->
                        <div class="text-center mt-4">

                        </div>

                    </form>
                    <div id="messages"></div>
                    <!-- end form-->
                </div>

                <!-- Footer-->
                <footer class="footer footer-alt">
                    <p class="text-muted">Don't have an account?
                        <a href="pages-register-2.html" class="text-muted ms-1"><b>Sign Up</b></a>
                    </p>
                </footer>

            </div> <!-- end .card-body -->
        </div>
        <!-- end auth-fluid-form-box-->

        <!-- Auth fluid right content -->
        <div class="auth-fluid-right text-center">
            <div class="auth-user-testimonial">
                <h2 class="mb-3">I love the color!</h2>
                <p class="lead"><i class="mdi mdi-format-quote-open"></i> It's a elegent templete. I love it very
                    much! . <i class="mdi mdi-format-quote-close"></i>
                </p>
                <p>
                    - Hyper Admin User
                </p>
            </div> <!-- end auth-user-testimonial-->
        </div>
        <!-- end Auth fluid right content -->
    </div>

@endsection
@section('scripts')
    <script type="module">
        import Authenticate from '/services/Auth/AuthService.js'
        /* Validamos el formulario */

        /* Funcion que envia el post */
        $.validator.setDefaults({
            submitHandler: function() {
                let formData = {};
                $("#login-form").serializeArray().map(function(x) {
                    formData[x.name] = x.value;
                });
                const url = `/api/authenticate/login`;
                Authenticate.setAuthenticate(url, formData);
                return false;
            }
        })

        $(document).ready(function() {
            $("#login-form").validate({
                rules: {
                    email: "required",
                    password: "required"
                },
                messages: {
                    email: "Este campo es requerido",
                    password: "Este campo es requerido"
                }
            });
        });
    </script>
@endsection
