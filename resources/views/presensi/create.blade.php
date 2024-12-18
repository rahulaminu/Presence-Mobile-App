@extends ('layout.presensi')
@section('header')
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
    <!-- * App Header -->
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
            height: 200px;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
            var map = L.map('map').setView([position.coords.latitude, position.coords.longitude], 16);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);
            var marker = L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
            var circle = L.circle([position.coords.latitude, position.coords.longitude], { //Mengatur Coor untuk Radius
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: 500 //Mengatur Jarak Radius (Satuanya Meter)
            }).addTo(map);
        }

        function errorCallback() {

        }

        $('#tagabsen').click(function(e) {
            Webcam.snap(function(uri) {
                image = uri
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
                        swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: respond.error,
                            confirmButtonText: 'OK'
                        })
                        setTimeout("location.href='/dashboard'", 3000);
                    } else {
                        swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: respond.message,
                            confirmButtonText: 'OK'
                        });
                        setTimeout("location.href='/dashboard'", 3000);
                    }
                }
            })
        })
    </script>
@endpush
