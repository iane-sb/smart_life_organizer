@extends('layouts.app')

@section('title', 'Edit Post')
@section('page-title', 'Edit Fitness Post ✏️')

@section('content')
<div style="display:flex; flex-direction:column; gap:24px; max-width: 640px; margin: 0 auto;">

    <div class="glass-card animate-in" style="padding:0;">
        <div class="modal-header" style="border-bottom:1px solid var(--border);">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:8px; background:var(--gradient-accent); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="pencil" style="color:white; width:18px; height:18px;"></i>
                </div>
                <span style="font-weight:700; font-size:16px; color:var(--text-primary);">Edit Your Post</span>
            </div>
            <a href="{{ route('fitness.index') }}" class="btn-icon" title="Cancel editing">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('fitness.update', $post) }}" enctype="multipart/form-data" style="padding:20px; display:flex; flex-direction:column; gap:16px;">
            @csrf
            @method('PUT')

            <!-- Workout Type -->
            <div class="form-group">
                <label class="form-label" for="workout_type">Workout Category *</label>
                <select name="workout_type" id="workout_type" class="form-control" required>
                    @foreach($workoutTypes as $key => $label)
                        <option value="{{ $key }}" {{ $post->workout_type === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('workout_type') <span style="color:#f87171; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <!-- Optional Metrics Row -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;" class="form-group">
                <div>
                    <label class="form-label" for="duration_minutes">Duration (Minutes)</label>
                    <div class="input-icon-wrap" style="position:relative;">
                        <i data-lucide="clock" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-muted); pointer-events:none;"></i>
                        <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" placeholder="e.g. 45"
                               min="1" max="1440" style="padding-left:36px;"
                               value="{{ old('duration_minutes', $post->duration_minutes) }}">
                    </div>
                    @error('duration_minutes') <span style="color:#f87171; font-size:12px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label" for="calories_burned">Calories Burned (kcal)</label>
                    <div class="input-icon-wrap" style="position:relative;">
                        <i data-lucide="flame" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--text-muted); pointer-events:none;"></i>
                        <input type="number" name="calories_burned" id="calories_burned" class="form-control" placeholder="e.g. 350"
                               min="1" max="20000" style="padding-left:36px;"
                               value="{{ old('calories_burned', $post->calories_burned) }}">
                    </div>
                    @error('calories_burned') <span style="color:#f87171; font-size:12px;">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Current Image -->
            @if($post->image_path)
                <div class="form-group">
                    <label class="form-label">Current Photo</label>
                    <div style="border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--border); max-height:200px; background:#000; display:flex; align-items:center; justify-content:center;">
                        <img src="{{ $post->image_url }}" alt="Current photo"
                             style="width:100%; height:auto; max-height:200px; object-fit:contain; display:block;">
                    </div>
                </div>
            @endif

            <!-- Replace Photo -->
            <div class="form-group">
                <label class="form-label">{{ $post->image_path ? 'Replace Photo (Optional)' : 'Workout Photo (Optional)' }}</label>
                <div style="border:1px dashed var(--border); border-radius:var(--radius-md); padding:16px; background:rgba(255,255,255,0.02); text-align:center;">
                    <label class="btn btn-ghost btn-sm" style="cursor:pointer; display:inline-flex; align-items:center; gap:6px;">
                        <i data-lucide="upload" style="width:14px; height:14px;"></i>
                        <span>Select Image (JPG, PNG, WEBP max 5MB)</span>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" style="display:none;">
                    </label>
                </div>
                @error('image') <span style="color:#f87171; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <!-- Caption -->
            <div class="form-group">
                <label class="form-label" for="caption">Workout Caption / Story *</label>
                <textarea name="caption" id="caption" class="form-control" rows="4" required
                          placeholder="How did today's workout feel?">{{ old('caption', $post->caption) }}</textarea>
                @error('caption') <span style="color:#f87171; font-size:12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; padding-top:8px; border-top:1px solid var(--border);">
                <a href="{{ route('fitness.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="check" style="width:14px;height:14px;"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
