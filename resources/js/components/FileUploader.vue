<script setup>
import vueFilePond from 'vue-filepond';
import 'filepond/dist/filepond.min.css';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import axios from 'axios';

const FilePond = vueFilePond(FilePondPluginFileValidateSize);

const serverConfig = {
  process: {
    url: '/api/upload',
    method: 'POST',
    withCredentials: false,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    ondata: (formData) => {
      const file = formData.get('file');
      const chunkIndex = formData.get('chunkIndex') || 0;
      const totalChunks = Math.ceil(file.size / 5000000); // 5MB chunks
      const identifier = btoa(file.name + file.size);
      const fileName = file.name;

      formData.append('chunkIndex', chunkIndex);
      formData.append('totalChunks', totalChunks);
      formData.append('identifier', identifier);
      formData.append('name', fileName);

      return formData;
    },
    onload: res => console.log('Upload complete', res),
    onerror: err => console.error('Upload failed', err),
  },
  revert: '/api/upload/revert',
  remove: (source, load, error) => {
    axios.post('/api/upload/delete', { file: source })
      .then(() => load())
      .catch(() => error('Delete failed'));
  }
};
</script>

<template>
  <div class="max-w-xl mx-auto p-6 bg-white shadow rounded mt-12">
    <h2 class="text-2xl font-bold mb-4">Upload Large File</h2>
    <FilePond
      name="file"
      ref="pond"
      :chunkUploads="true"
      :server="serverConfig"
      :allowMultiple="false"
      label-idle="Drag & Drop your file or <span class='text-blue-600'>browse</span>"
      :maxFileSize="'6000MB'"
    />
  </div>
</template>
