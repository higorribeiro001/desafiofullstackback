<x-mail::message>
<h1 class="text-xl font-semibold text-gray-900">{{ $subject }}</h1>

{{ $line }}

<x-mail::button style="background-color: #4F46E5" :url="config('app.url')">
Acessar E-inov
</x-mail::button>

Atenciosamente,<br>
E-inov
</x-mail::message>
