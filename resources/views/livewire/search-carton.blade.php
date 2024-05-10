<div class="max-w-7xl px-4 py-2 sm:px-6 lg:px-8 lg:py-4 mx-auto max-h-screen">

    <div class="max-w-2xl mx-auto card lg:card-side bg-base-100 shadow-md">

        <figure><img src="{{ asset('storage/images/carton-box-illu04.jpg') }}" style="height: 460px;" class="hidden md:block"></figure>
        <div class="card-body">
            <h2 class="card-title">Select Carton Box</h2>
            <p>Check & select the availability of carton boxes to be validated.</p>

            <x-mary-form id="boxCheck" wire:submit="check">
                <x-input icon="qrcode" label="Box Code" placeholder="Scan the barcode" wire:model="boxForm.box_code" autofocus autocomplete="off" />
                @if ($showExtraForm === true)
                <x-select label="PO" :async-data="route('api.carton-po', ['box_code' => $boxForm['box_code']])" option-value="po" option-label="po" wire:model.live="extraForm.selectedPo" />
                @endif
                @if ($extraForm['selectedPo'] !== '-- Select PO --')
                <x-select label="Carton Number" :async-data="route('api.carton-number', ['box_code' => $boxForm['box_code'],'po'=>$extraForm['selectedPo']])" option-value="carton_number" option-label="carton_number" wire:model="extraForm.selectedCartonNumber" />
                @endif

                <x-slot:actions>
                    <x-button icon="switch-horizontal" negative label="Reset" spinner="save" href='/accuracy/carton/check' />
                    <x-button icon="search" primary label="Search" type="submit" spinner="save" />

                </x-slot:actions>
            </x-mary-form>

        </div>

    </div>
    <div id="reader" width="600px"></div>
</div>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        const html5QrCode = new Html5Qrcode("reader");

        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            /* handle success */
            console.log(`Scan result: ${decodedText}`, decodedResult);
            window.getElementById('box_code').value = decodedText;

            document.getElementById('box_code').value = decodedText;

            // ...
            html5QrcodeScanner.clear();
        };
        const config = {
            fps: 10,
            qrbox: 250
        };
        // Select front camera or fail with `OverconstrainedError`.
        html5QrCode.start({
            facingMode: {
                exact: "environment"
            }
        }, config, qrCodeSuccessCallback);
        //html5QrCode.start({ facingMode: { exact: "user"} }, config, qrCodeSuccessCallback);
    })
</script>
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('swal', (event) => {
            const data = event
            swal.fire({
                icon: data[0]['icon'],
                title: data[0]['title'],
                text: data[0]['text'],
            })
        })
    })
</script>
