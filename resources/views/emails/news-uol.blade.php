<x-mail::message>
<h1 class="text-xl font-semibold text-gray-900">{{ $subject }}</h1>

{!! $line !!}

<x-mail::button :url="$link">
Acessar Notícia
</x-mail::button>

<span>{{ $date }}</span>

<span>Atenciosamente,</span><br>
<span>E-inov</span>
</x-mail::message>
