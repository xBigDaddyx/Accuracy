<div class="card overflow-x-auto bg-white rounded-xl shadow-md p-4 max-h-[30rem] mt-4">
    <div class="overflow-x-auto">
        <h2 class="card-title">Ratio Attributes</h2>
        <p>Below attributes need to be validated each polybags</p>
        <table class="table">
            <thead>
                <tr class="font-bold text-primary-content">
                    <th>Attribute ID</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Quantity</th>

                </tr>
            </thead>
            <tbody>
                @foreach(session()->get('carton.attributes') as $attribute)
                <tr>
                    <td>{{$attribute['id']}}</td>
                    <td>{{$attribute['size']}}</td>
                    <td>{{$attribute['color']}}</td>
                    <td>{{$attribute['quantity']}}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
