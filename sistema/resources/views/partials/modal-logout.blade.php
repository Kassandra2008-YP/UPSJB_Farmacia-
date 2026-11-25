<div class="modal fade" id="logoutModal" tabindex="-1"
     aria-labelledby="logoutModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-sm">

        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-body text-center p-4">

                <div class="rounded-circle bg-danger-subtle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:65px;height:65px">

                    <i class="bi bi-box-arrow-right text-danger fs-2"></i>

                </div>

                <h5 class="fw-bold mb-2" id="logoutModalLabel">
                    ¿Cerrar sesión?
                </h5>

                <p class="text-secondary small mb-0">
                    ¿Estás seguro de que deseas salir del sistema?
                    Tu sesión actual se cerrará.
                </p>

            </div>

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <div class="modal-footer border-top justify-content-center py-3 gap-2">

                    <button type="button"
                            class="btn btn-sm btn-outline-secondary px-4"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-sm btn-danger px-4">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        Salir
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
