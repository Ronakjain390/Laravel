<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
            ©
            <script>
                document.write(new Date().getFullYear());
            </script>
            <?php
            $data = generalSetting();
            ?>
            @if(isset($data->copyright_text))
            <a href="#" target="_blank" class="footer-link fw-bolder">{{$data->copyright_text}}</a>
            @endif
        </div>
    </div>
</footer>