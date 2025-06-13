<div>
    <table class="w-full border">
        <thead>
        <tr class="bg-gray-100">
            @foreach($columns as $column)
                <th class="px-4 py-2 border">{{ $column }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($tableData as $row)
            <tr>
                @for($i = 0; $i < $inputCount; $i++)
                    <td class="px-4 py-2 border text-center">{{ $row['input_' . $i] }}</td>
                @endfor
                <td class="px-4 py-2 border text-center">
                    <input type="hidden" name="truth_table_entries[{{ $row['id'] }}][combined]"
                           value="{{ $row['combined'] }}">
                    <div x-data="{ output: @entangle('data.truth_table_entries.' . $row['id'] . '.output').defer }">
                        <button type="button"
                                x-on:click="output = output == 1 ? 0 : 1"
                                class="w-8 h-8 rounded-full focus:outline-none"
                                x-bind:class="output == 1 ? 'bg-green-500' : 'bg-red-500'"
                        >
                            <span x-text="output" class="text-white font-bold"></span>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
