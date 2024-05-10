<div class=" px-4 py-10 sm:px-6 lg:px-8 lg:py-14 ">
    <div class="grid md:grid-cols-6 gap-4  mb-4 ">
        <div class="alert alert-warning shadow-sm col-span-5">
            <x-heroicon-o-exclamation-triangle class="stroke-current shrink-0 h-6 w-6" />
            <div>
                <h3 class="font-bold">This carton box is {{ session()->get('carton.type') }} </h3>
                @if (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX')
                <div class="text-xs">After finish validating garment tag, close by scanning polybag barcode or carton
                    box barcode.</div>
                @elseif(session()->get('carton.type') === 'SOLID')
                <div class="text-xs">Validating each polybags inside the carton box.</div>
                @endif

            </div>

        </div>
        <div class="hidden md:block">
            <div class="alert alert-error text-white shadow-sm justify-self-end ">
                <x-heroicon-o-clock class="stroke-current shrink-0 h-6 w-6" />
                <div>
                    <h3 class="font-bold">Clock</h3>
                    <span id="clock" onload="currentTime()"></span>
                </div>

            </div>
        </div>

    </div>

    @if (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX')
    <div class="max-w-screen mb-8">
        @livewire('accuracy-polybag-stats', ['carton'=>$carton ,'type'=>$type ,'polybags'=>$polybags ,'tags'=>$tags])
    </div>
    @endif
    <!-- <div x-data="progressBar" class="px-4 py-6 sm:px-6 lg:p-8">
        <h4 class="sr-only">Status</h4>
        <div class="mt-6" aria-hidden="true">
            <div class="overflow-hidden rounded-full bg-gray-200">
                <div class="h-2 rounded-full bg-blue-600 transition-all transition-2s ease-in-out" :style="{width: progressBarWidth + '%'}"></div>
            </div>
            <div class='mt-6 hidden grid-cols-4 text-sm font-medium text-gray-600 sm:grid'>
                <div :class="{ 'text-blue-600 font-bold': currentStatus === '' }" class="text-blue-600">
                    Preparing ({{session()->get('carton.type')}} Principle)
                </div>
                <div :class="{ 'text-blue-600 font-bold': currentStatus === 'validating' }" class="text-center">
                    Validating polybag
                </div>
                <div :class="{ 'text-blue-600 font-bold': currentStatus === 'almost-completed' }" class="text-center">
                    Almost Completed
                </div>
                <div :class="{ 'text-blue-600 font-bold': currentStatus === 'completed' }" class="text-right">
                    Completed
                </div>
            </div>
        </div>
    </div> -->
    <div class="grid md:grid-cols-12 gap-4">

        <div class="card lg:card-side shadow-md max-h-80 mb-4 mt-4 col-span-8 bg-base-100">
            @if (session()->get('carton.type') === 'SOLID')
            <figure><img src="{{ asset('storage/images/carton-box-illu02.jpg') }}" style="height:400px;" class="hidden md:block"></figure>
            @elseif (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX')
            <figure><img src="{{ asset('storage/images/carton-box-illu06.jpg') }}" style="height:500px;" class="hidden md:block"></figure>
            @endif

            <div class="card-body">

                <x-mary-form wire:submit="validation">
                    @if (session()->get('carton.type') === 'SOLID')
                    @if ($completed)
                    <x-input icon="qrcode" autofocus label="Polybag barcode" wire:model="form.polybag_barcode" hint="Please scan polybag barcode now." disabled placeholder="your name" />

                    @else
                    <x-input icon="qrcode" autofocus label="Polybag barcode" wire:model.live="form.polybag_barcode" hint="Please scan polybag barcode now." placeholder="your name" />

                    @endif
                    @elseif (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX')
                    @if ($completed)
                    <x-input icon="qrcode" autofocus label="Garment tag barcode" wire:model="form.tag_barcode" hint="Please scan garment tag barcode now." placeholder="your name" disable />

                    @elseif (session()->get('polybag.status') === 1)
                    <x-input icon="qrcode" autofocus label="Polybag/Carton barcode" wire:model="form.polybag_barcode" hint="Please scan polybag/carton barcode now." placeholder="your name" />
                    @else
                    <x-input icon="qrcode" autofocus label="Garment tag barcode" wire:model="form.tag_barcode" hint="Please scan garment tag barcode now." placeholder="your name" />

                    @endif
                    @endif


                    <x-slot:actions>

                        <x-mary-button label="Show Table" icon="m-window" class="btn-info" spinner="save" wire:click="toggleShowTable" />
                        <x-mary-button label="Reset" icon="m-arrow-path" class="btn-primary" spinner="save" :link="route('accuracy.check.carton.release')" />
                    </x-slot:actions>
                </x-mary-form>

            </div>

        </div>
        @if (session()->get('carton.type') === 'RATIO' || session()->get('carton.type') === 'MIX')
        <div class="col-span-4">
            @livewire('accuracy-polybag-attributes' ,['carton'=>$carton,'type'=> $type, 'polybags'=>$polybags])

        </div>
        @endif
        @if (session()->get('carton.type') === 'SOLID')
        @livewire('accuracy-polybag-stats' ,['carton'=>$carton,'type'=> $type, 'polybags'=>$polybags])
        @endif
    </div>

    @if ($showTable)
    <div wire:transition class="max-w-7xl mt-8">

        <livewire:kanban.validation-table :$carton />

    </div>
    @endif
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('progressBar', () => ({
            progressBarWidth: 1,
            currentStatus: '',
            cartonId: '',
            init() {
                this.cartonId = '{{$carton->id}}';
                this.currentStatus = '{{$carton->status}}';
                this.updateProgressBar();

                Echo.private('accuracy.' + this.cartonId)
                    .listen('CartonBoxStatusUpdated', (e) => {
                        this.currentStatus = e.status;
                        this.updateProgressBar();
                    });
            },
            updateProgressBar() {
                if (this.currentStatus === 'validating') {
                    this.progressBarWidth = 10;
                } else if (this.currentStatus === 'almost-completed') {
                    this.progressBarWidth = 50;
                } else if (this.currentStatus === 'completed') {
                    this.progressBarWidth = 100;
                }
            }
        }));
    });
</script>

<script>
    function currentTime() {
        let date = new Date();
        let hh = date.getHours();
        let mm = date.getMinutes();
        let ss = date.getSeconds();
        let session = "AM";

        if (hh === 0) {
            hh = 12;
        }
        if (hh > 12) {
            hh = hh - 12;
            session = "PM";
        }

        hh = (hh < 10) ? "0" + hh : hh;
        mm = (mm < 10) ? "0" + mm : mm;
        ss = (ss < 10) ? "0" + ss : ss;

        let time = hh + ":" + mm + ":" + ss + " " + session;

        document.getElementById("clock").innerText = time;
        let t = setTimeout(function() {
            currentTime()
        }, 1000);
    }

    currentTime();
</script>
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('swal', (event) => {
            const data = event
            swal.fire({
                icon: data[0]['icon'],
                title: data[0]['title'],
                text: data[0]['text'],
                showConfirmButton: data[0]['showConfirmButton'],
                allowOutsideClick: data[0]['allowOutsideClick'],
            })
        })
    })
</script>
