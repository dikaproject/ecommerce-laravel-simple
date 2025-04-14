<!-- resources/views/components/footer.blade.php -->
<footer class="pt-5 pb-3 mt-5" style="background: linear-gradient(135deg, #e62c3b, #c82333); color: white;">
    <div class="container">
        <div class="row gx-5">
            <div class="col-md-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" width="36" height="36" class="d-inline-block me-2">
                    <h4 class="mb-0 fw-bold">Izzi Craft</h4>
                </div>
                <p class="opacity-80 fw-light">Menyediakan berbagai kebutuhan craft dan buket premium untuk mendukung ide kreatif Anda dengan harga terjangkau dan kualitas terbaik.</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5 class="mb-3 fw-bold">Hubungi Kami</h5>
                <div class="mb-3 d-flex align-items-center">
                    <div class="me-3 text-center" style="width: 32px;">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <span>+62 123 456 7890</span>
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <div class="me-3 text-center" style="width: 32px;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <span>info@izzicraft.com</span>
                </div>
                <div class="mb-3 d-flex align-items-center">
                    <div class="me-3 text-center" style="width: 32px;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span>Jawa Tengah, Indonesia</span>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <h5 class="mb-3 fw-bold">Ikuti Kami</h5>
                <div class="social-links mb-4">
                    <a href="#" class="btn btn-light btn-sm rounded-circle me-2 shadow-sm" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="btn btn-light btn-sm rounded-circle me-2 shadow-sm" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-light btn-sm rounded-circle me-2 shadow-sm" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-light btn-sm rounded-circle me-2 shadow-sm" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.3s;">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
                
                <h5 class="mb-3 fw-bold">Jam Operasional</h5>
                <div class="d-flex justify-content-between opacity-80 fw-light mb-1">
                    <span>Senin - Jumat:</span>
                    <span>08.00 - 17.00</span>
                </div>
                <div class="d-flex justify-content-between opacity-80 fw-light mb-1">
                    <span>Sabtu:</span>
                    <span>09.00 - 15.00</span>
                </div>
                <div class="d-flex justify-content-between opacity-80 fw-light">
                    <span>Minggu:</span>
                    <span>Tutup</span>
                </div>
            </div>
        </div>
        
        <hr class="my-4 opacity-25">
        
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <p class="mb-0 opacity-75 small">Copyright &copy; {{ date('Y') }} Izzi Craft. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-white text-decoration-none me-3 opacity-75 small">Syarat & Ketentuan</a>
                <a href="#" class="text-white text-decoration-none opacity-75 small">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</footer>