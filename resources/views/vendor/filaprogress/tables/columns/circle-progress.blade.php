@php
    $progress = $getState();

    if ($progress == 100) {
        $progressColor = '#2980b9';
    } elseif ($progress > 50) {
        $progressColor = '#27ae60';
    } elseif ($progress > 25) {
        $progressColor = '#f39c12';
    } else {
        $progressColor = '#e74c3c';
    }

    $displayProgress = $progress == 100 ? number_format($progress, 0) : number_format($progress, 2);
@endphp

<div class="progress-circle"
    style="
    background: conic-gradient(
        {{ $progressColor }} {{ $displayProgress * 3.6 }}deg,
        #e5e7eb {{ $displayProgress * 3.6 }}deg
    );">
    @if ($column instanceof \IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress && $column->getCanShow())
        <small>{{ $displayProgress }}%</small>
    @endif
</div>

<style>
    .progress-circle {
        position: relative;
        width: 55px;
        height: 55px;
        margin: 10px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fff;
        font-weight: 600;
    }

    .progress-circle::before {
        content: '';
        position: absolute;
        width: 70%;
        height: 70%;
        background-color: #fff;
        border-radius: 50%;
        z-index: 1;
    }

    .progress-circle small {
        position: absolute;
        color: black;
        font-size: 8pt;
        z-index: 2;
    }
</style>