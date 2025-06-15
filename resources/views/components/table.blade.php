@props(['title', 'employees', 'criteria', 'values', 'precision' => 2])

<div class="mt-6">
    <h3 class="font-semibold text-md mb-2">{{ $title }}</h3>
    <div class="overflow-x-auto border rounded-lg">
        <table class="min-w-full text-sm text-left border border-gray-200">
            <thead class="bg-gray-100 text-gray-700 font-semibold">
                <tr>
                    <th class="px-4 py-2 border-b">Employee</th>
                    @foreach ($criteria as $criterion)
                        <th class="px-4 py-2 border-b text-center">{{ $criterion->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border-b font-medium text-gray-800">{{ $employee->name }}</td>
                        @foreach ($criteria as $criterion)
                            <td class="px-4 py-2 border-b text-center">
                                {{ number_format($values[$employee->id][$criterion->id] ?? 0, $precision) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
