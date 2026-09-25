<x-mail::message>
# Oi, xxxxx, 
Segue dados referente a Potênc<b>IA</b> dos seus colaboradores.
<x-mail::table>
| Cargo       | Ação  | Qtd  |
| :------------- |:--------|--------:|
{{-- @foreach ($data as $item)
| {{ $item['type'] }} | {{ $item['action'] }} | {{ $item['volume'] }} |
@endforeach --}}
</x-mail::table>
<p>&nbsp;</p>
<x-mail::button >
Detalhes no Ahtlas
</x-mail::button>
<p>&nbsp;</p>
<x-mail::panel>
Responsável pelas informações: <a href="mailto:responsavel@example.com?subject=Potênc-IA">Equipe responsável</a>
</x-mail::panel>
<x-mail::subscript> </x-mail::subscript>
</x-mail::message>
