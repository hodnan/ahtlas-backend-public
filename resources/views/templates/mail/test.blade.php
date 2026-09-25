<x-mail::message>
  
# Extrato da Central de Controle e Performance (CCP)
<b>Operação:</b> -5017 - Sac Fibra (grupo) <br>
<b>Indicador:</b> 491 - Aderencia - NBA (%)
 <x-mail::table>   
    | Data| Meta | Resultado |
    | :-------: |:-------:| :-------: |
    |2021-09-19|96|98.09|
</x-mail::table>

<x-mail::button url="http://localhost:3000/management/control-center/tracking?filter=true&month_ref=2024-09">
Detalhes no Ahtlas
</x-mail::button>

<x-mail::table>   
    |       |    
    | :-------------: |
    | A CCP monitora os indicadores e gera extratos comparativos de performance, identificando os colaboradores fora da meta. Com foco na melhoria dos resultados operacionais, os líderes recebem a lista dos colaboradores descolados conforme o stage para desenvolver planos de ações corretivas que assegurem a recuperação da performance dos colaboradores e o cumprimento dos indicadores contratuais.|
    
</x-mail::table>
   

</x-mail::message>

{{-- <x-mail::table>
| Cargo       | Ação  | Qtd  |
| :------------- |:--------|--------:| --}}
{{-- @foreach ($data as $item)
| {{ $item['type'] }} | {{ $item['action'] }} | {{ $item['volume'] }} |
@endforeach --}}
{{-- </x-mail::table>
<p>&nbsp;</p>
<x-mail::button >
Detalhes no Ahtlas
</x-mail::button>
<p>&nbsp;</p>

<x-mail::subscript> </x-mail::subscript>
</x-mail::message> --}}
