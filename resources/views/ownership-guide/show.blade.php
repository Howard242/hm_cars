@extends('layouts.app')

@section('title', $guide->title)
@section('meta_description', \Illuminate\Support\Str::limit($guide->introduction, 155))

@section('content')
<style>
    .ownership-hero { padding: 64px 0 48px; background: linear-gradient(120deg, #f5f5ed, #eaf3ec); }
    .ownership-hero h1 { max-width: 820px; margin: 10px 0; font-size: clamp(2.2rem, 5vw, 3.6rem); line-height: 1.1; }
    .ownership-hero p { max-width: 760px; color: #52645a; font-size: 1.08rem; line-height: 1.8; }
    .ownership-steps { display: grid; gap: 18px; padding-block: 42px 64px; }
    .ownership-step { display: grid; grid-template-columns: 72px minmax(0, 1fr); gap: 18px; padding: 24px; border: 1px solid #e2e7e3; border-radius: 16px; background: white; box-shadow: 0 12px 30px rgba(25,45,35,.05); }
    .ownership-step-number { display: grid; place-items: center; width: 52px; height: 52px; border-radius: 16px; background: #e6f1eb; color: #176b4d; font-weight: 850; font-size: 1.1rem; }
    .ownership-step h2 { margin: 0 0 8px; font-size: 1.3rem; }
    .ownership-step p { margin: 0; color: #52645a; line-height: 1.8; white-space: pre-line; }
    .ownership-step img { width: 100%; max-height: 360px; margin-top: 18px; border-radius: 12px; object-fit: cover; }
    .ownership-disclaimer { margin-bottom: 48px; padding: 18px 20px; border-left: 4px solid #e6b45a; border-radius: 8px; background: #fff8e9; color: #5c5038; }
    .ownership-preview { margin: 20px auto 0; padding: 14px 18px; border: 1px solid #e6b45a; border-radius: 10px; background: #fff8e9; color: #5c5038; }
    @media (max-width: 520px) { .ownership-hero { padding: 42px 0 30px; } .ownership-step { grid-template-columns: 46px minmax(0, 1fr); gap: 12px; padding: 17px; } .ownership-step-number { width: 42px; height: 42px; border-radius: 13px; } }
</style>

@if ($preview)
    <div class="container ownership-preview" role="status">
        <strong>Admin preview:</strong> This is a preview. Unpublished content is visible only to administrators.
        <a href="{{ route('admin.ownership-guide.edit') }}">Return to editor</a>
    </div>
@endif

<section class="ownership-hero">
    <div class="container">
        <span class="eyebrow">A PRACTICAL BUYER'S GUIDE</span>
        <h1>{{ $guide->title }}</h1>
        <p>{{ $guide->introduction }}</p>
        <a class="button button-primary" href="{{ route('vehicles.index') }}">Browse cars <span aria-hidden="true">→</span></a>
    </div>
</section>

<section class="container ownership-steps" aria-label="Car ownership steps">
    @foreach ($guide->steps as $step)
        <article class="ownership-step">
            <div class="ownership-step-number" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
            <div>
                <h2>{{ $step->title }}</h2>
                <p>{{ $step->body }}</p>
                @if ($step->image_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($step->image_path) }}" alt="{{ $step->title }}">
                @endif
            </div>
        </article>
    @endforeach
</section>

<aside class="container ownership-disclaimer">
    <strong>Please note:</strong> This guide is general information, not legal or financial advice. Procedures, fees and requirements may change. Confirm the latest details with NTSA, eCitizen, your insurer or another relevant official provider before completing a purchase.
</aside>
@endsection
