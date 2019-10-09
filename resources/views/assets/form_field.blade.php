<div class="field is-horizontal">
	<div class="field-label is-normal">
		<label class="label" for="{{ $label }}_id">{{ $label }}</label>
	</div>
	<div class="field-body">
		<div class="field {{ isset($static) ? 'has-addons' : '' }}">
			<div class="control">
				@if (isset($element))
					@if ($element === 'select')
						<div class="select">
							<select
								name="{{ $name }}"
								id="{{ $label }}_id"
								class="{{ $errors->has($name) ? ' is-danger' : '' }}"
								{{ (isset($disabled) && $disabled) ? 'disabled' : '' }}
								>

								@foreach ($options as $key => $option)
									@if(isset($select_no_key) && $select_no_key)
										{{-- No key, so option is the same as key. --}}
										@php ($key = $option)
									@endif

									@php ($selected = ($key === old($name, $value)) ? 'selected' : '')

									{{-- Styles --}}
									@php ($styles = [])
									@isset($set_select_option_as_font)
										@php ($styles[] = "font-family: '" . $option . "'")
									@endisset

									<option
										value="{{ $key }}"
										{{ $selected }}
										style="{{ implode(';', $styles) }}"
										>{{ $option }}</option>
								@endforeach
							</select>
						</div>
					@endif
				@else
					<input
						class="input {{ $errors->has($name) ? ' is-danger' : '' }}"
						type="{{ $type ?? 'text' }}"
						name="{{ $name }}"
						value="{{ old($name, $value ?? '') }}"
						placeholder="{{ $placeholder ?? '' }}"
						{{ (isset($disabled) && $disabled) ? 'disabled' : '' }}
						step="{{ $step ?? 1 }}"
						min="{{ $min ?? "" }}"
						max="{{ $max ?? "" }}"
						id="{{ $label }}_id">
				@endif
			</div>
			@isset($static)
				<p class="control">
					<a class="button is-static">{{ $static }}</a>
				</p>
			@endisset
			@if ($errors->has($name))
				<p class="help is-danger" role="alert">
					{{ $errors->first($name) }}
				</p>
			@endif
		</div>
	</div>
</div>