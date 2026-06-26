<template>
    <div class="space-y-4">
        <div>
            <Label for="logo">Restaurant Logo</Label>
            <p class="mt-1 text-sm text-muted-foreground">
                Upload your restaurant logo. Recommended size: 400x400px (PNG,
                JPG up to 2MB)
            </p>
        </div>

        <!-- Upload Area -->
        <div
            @click="triggerFileInput"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            :class="[
                'cursor-pointer rounded-lg border-2 border-dashed p-6 transition-colors',
                isDragging
                    ? 'border-primary bg-primary/5'
                    : 'border-muted-foreground/25 hover:border-primary/50',
            ]"
        >
            <div class="flex flex-col items-center justify-center text-center">
                <div
                    class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-muted"
                >
                    <CloudUpload />
                </div>
                <p class="mb-1 text-sm font-medium">
                    Click to upload or drag and drop
                </p>
                <p class="text-xs text-muted-foreground">PNG, JPG up to 2MB</p>
            </div>
        </div>

        <!-- Hidden file input -->
        <input
            ref="fileInput"
            id="logo"
            type="file"
            accept="image/png,image/jpeg,image/jpg"
            @change="handleFileSelect"
            class="hidden"
            :name="name"
        />

        <!-- Preview Section -->
        <div v-if="previewUrl || existingLogo" class="space-y-3">
            <div
                class="flex items-start gap-4 rounded-lg border bg-muted/30 p-4"
            >
                <!-- Image Preview -->
                <div
                    class="flex h-20 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-white"
                >
                    <img
                        :src="previewUrl || existingLogo"
                        alt="Logo preview"
                        class="h-full w-full object-contain"
                    />
                </div>

                <!-- File Info -->
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">
                                {{ fileName }}
                            </p>
                            <p
                                v-if="fileSize"
                                class="mt-0.5 text-xs text-muted-foreground"
                            >
                                {{ fileSize }}
                            </p>
                        </div>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-8 w-8 flex-shrink-0"
                            @click="handleRemove"
                            type="button"
                        >
                            <X />
                        </Button>
                    </div>

                    <!-- Upload Progress (if uploading) -->
                    <div v-if="isUploading" class="mt-3">
                        <div
                            class="mb-1 flex items-center justify-between text-xs"
                        >
                            <span class="text-muted-foreground"
                                >Uploading...</span
                            >
                            <span class="font-medium"
                                >{{ uploadProgress }}%</span
                            >
                        </div>
                        <div
                            class="h-1.5 w-full overflow-hidden rounded-full bg-muted"
                        >
                            <div
                                class="h-full bg-primary transition-all duration-300"
                                :style="{ width: uploadProgress + '%' }"
                            ></div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div
                        v-if="uploadSuccess"
                        class="mt-2 flex items-center gap-1.5 text-xs text-green-600"
                    >
                        <Check size="15" />
                        <span>Upload successful</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <Alert v-if="errorMessage" variant="destructive">
            <CircleAlert />
            <!-- <AlertTitle>Error</AlertTitle> -->
            <AlertDescription>{{ errorMessage }}</AlertDescription>
        </Alert>
    </div>
</template>

<script setup>
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Check, CircleAlert, CloudUpload, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    existingLogo: {
        type: String,
        default: null,
    },
    progress: {
        type: Number,
        default: 0,
    },
    name: {
        type: String,
        default: 'file',
    },
    maxSize: {
        type: Number,
        default: 2 * 1024 * 1024, // 2MB
    },
});

const emit = defineEmits(['upload', 'remove']);

const fileInput = ref(null);
const selectedFile = ref(null);
const previewUrl = ref(null);
const isDragging = ref(false);
const errorMessage = ref('');
const isUploading = ref(false);
const uploadProgress = ref(0);
const uploadSuccess = ref(false);

const fileName = computed(() => selectedFile.value?.name || '');
const fileSize = computed(() => {
    if (!selectedFile.value) return '';
    const size = selectedFile.value.size;
    if (size < 1024) return size + ' B';
    if (size < 1024 * 1024) return (size / 1024).toFixed(1) + ' KB';
    return (size / (1024 * 1024)).toFixed(1) + ' MB';
});

const triggerFileInput = () => {
    fileInput.value?.click();
};

const validateFile = (file) => {
    errorMessage.value = '';

    // Check file type
    const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
    if (!validTypes.includes(file.type)) {
        errorMessage.value = 'Please upload a PNG or JPG file';
        return false;
    }

    // Check file size
    if (file.size > props.maxSize) {
        errorMessage.value = `File size must be less than ${(props.maxSize / (1024 * 1024)).toFixed(0)}MB`;
        return false;
    }

    return true;
};

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        processFile(file);
    }
};

const handleDrop = (event) => {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    if (file) {
        processFile(file);
    }
};

const processFile = (file) => {
    if (!validateFile(file)) return;

    selectedFile.value = file;
    uploadSuccess.value = false;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        previewUrl.value = e.target.result;
        simulateUpload(file);
    };
    reader.readAsDataURL(file);
};

const simulateUpload = (file) => {
    // Simulate upload progress
    isUploading.value = true;
    uploadProgress.value = 0;

    const interval = setInterval(() => {
        uploadProgress.value += 10;
        if (uploadProgress.value >= 100) {
            clearInterval(interval);
            isUploading.value = false;
            uploadSuccess.value = true;
            emit('upload', file);
        }
    }, 150);
};

const handleRemove = () => {
    selectedFile.value = null;
    previewUrl.value = null;
    errorMessage.value = '';
    uploadSuccess.value = false;
    uploadProgress.value = 0;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
    emit('remove');
};
</script>
