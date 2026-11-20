@include('partials.form-open', ['action' => $role->exists ? route('roles.update', $role) : route('roles.store'), 'model' => $role])
  <fieldset>
    <x-field name="name" label="Role name" :value="$role->name" req span :lock="$readonly" />
    <x-field name="description" label="Description" type="textarea" :value="$role->description" span :lock="$readonly" />
  </fieldset>
</form>
