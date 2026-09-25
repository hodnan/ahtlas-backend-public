<x-mail::message>
# Extrato da Central de Controle e Performance (CCP) 
<b>Operação:</b> {{ $stage['sector_n1']['label'] }}  <br>
<b>Indicador:</b> {{ $stage['indicator']['label'] }} | <b>Direção:</b> {{$stage['indicator']['direction']['name']}}<br>
<b>Stage:</b> {{ $stage['stage']['id'] }} <br>
 <x-mail::table>   
    | Data| Meta | Resultado |
    | :-------: |:-------:| :-------: |
    |{{$stage['date_ref']}}|{{$stage['goal']}}|{{$stage['result']}}|
</x-mail::table>

<x-mail::button :url="$url">
Agentes ofensores
</x-mail::button>

<x-mail::table>   
    |       |    
    | :-------------: |
    | A CCP monitora os indicadores e gera extratos comparativos de performance, identificando os colaboradores fora da meta. Com foco na melhoria dos resultados operacionais, os líderes recebem a lista dos colaboradores descolados conforme o stage para desenvolver planos de ações corretivas que assegurem a recuperação da performance dos colaboradores e o cumprimento dos indicadores contratuais.|
    
</x-mail::table>
   

</x-mail::message>
