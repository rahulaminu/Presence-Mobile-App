@extends ('layout.presensi')
@section('header')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">E-Presensi</div>
        <div class="right"></div>
    </div>
    <style>
        .camera,
        .camera video {
            display: inline-block;
            width: 100% !important;
            margin: auto;
            height: auto !important;
            border-radius: 15px;
        }

        #map {
            height: 320px;
        }
    </style>
@endsection
@section('content')
    <div class="row" style="margin-top : 70px;">
        <div class="col">
            <input type="hidden" id="lokasi">
            <div class="camera"></div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            @if ($cek > 0)
                @if ($sudahPulang)
                    <button class="btn btn-secondary btn-block" disabled>
                        Sudah Presensi Pulang
                    </button>
                @else
                    <button id="tagabsen" class="btn btn-danger btn-block">
                        <ion-icon name="camera-outline"></ion-icon>
                        Pulang
                    </button>
                @endif
            @else
                <button id="tagabsen" class="btn btn-primary btn-block">
                    <ion-icon name="camera-outline"></ion-icon>
                    Masuk
                </button>
            @endif
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <div id="map"></div>
        </div>
    </div>
@endsection
@push('myscript')
    <script>
        Webcam.set({
            height: 480,
            widht: 640,
            image_format: 'jpeg',
            jpeg_quality: 80
        });

        Webcam.attach('.camera');

        var lokasi = document.getElementById('lokasi');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
        }

        function successCallback(position) {
            lokasi.value = position.coords.latitude + "," + position.coords.longitude;
            
            // Koordinat lokasi yang diizinkan (ganti dengan koordinat sekolah)
            const kantorLocation = {
                lat: -3.439873,  // Ganti dengan latitude sekolah
                lng: 114.842238  // Ganti dengan longitude sekolah
            };
            
            // Inisialisasi map dengan posisi user
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 17);
            
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 17,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Marker untuk posisi user
            var userMarker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
            userMarker.bindPopup("Lokasi Anda").openPopup();

            // Marker untuk lokasi kantor/sekolah
            var kantorMarker = L.marker([kantorLocation.lat, kantorLocation.lng]).addTo(map);
            kantorMarker.bindPopup("Lokasi Kantor").openPopup();

            // Radius yang diizinkan
            var circle = L.circle([kantorLocation.lat, kantorLocation.lng], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: 100 // Radius dalam meter
            }).addTo(map);
        }

        function errorCallback() {
            Swal.fire({
                icon: 'error',
                title: 'Lokasi tidak ditemukan',
                text: 'Pastikan GPS Anda aktif',
                confirmButtonText: 'OK'
            });
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius bumi dalam km
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * 
                    Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const d = R * c;
            return d * 1000; // Konversi ke meter
        }

        function toRad(value) {
            return value * Math.PI / 180;
        }

        $('#tagabsen').click(function(e) {
            e.preventDefault();
            
            // Ambil koordinat user
            var userCoords = $('#lokasi').val().split(',');
            var userLat = parseFloat(userCoords[0]);
            var userLng = parseFloat(userCoords[1]);
            
            // Koordinat kantor/sekolah
            const kantorLocation = {
                lat: -3.439873,  // Ganti dengan latitude sekolah
                lng: 114.842238  // Ganti dengan longitude sekolah
            };
            
            // Hitung jarak
            var distance = calculateDistance(
                userLat, 
                userLng,
                kantorLocation.lat,
                kantorLocation.lng
            );
            
            // Validasi jarak (radius 100 meter)
            if (distance > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Di Luar Area',
                    text: 'Anda berada di luar area yang diizinkan. Jarak Anda ' + Math.round(distance) + ' meter dari lokasi',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Jika dalam radius yang diizinkan, lanjutkan proses presensi
            Webcam.snap(function(uri) {
                image = uri;
            });
            
            var lokasi = $('#lokasi').val();
            $.ajax({
                type: 'POST',
                url: '/presensi/store',
                data: {
                    _token: "{{ csrf_token() }}",
                    image: image,
                    lokasi: lokasi
                },
                cache: false,
                success: function(respond) {
                    if (respond.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: respond.error,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: respond.message,
                            confirmButtonText: 'OK'
                        });
                    }
                    setTimeout("location.href='/dashboard'", 3000);
                }
            });
        });
    </script>
@endpush
