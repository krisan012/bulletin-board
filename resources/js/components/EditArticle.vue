<template>
    <div class="container mt-5">
        <h1>Edit Article</h1>

        <!-- Article Edit Form -->
        <div v-if="isLoading" class="skeleton-loader">
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton skeleton-text"></div>
            <div class="skeleton skeleton-text"></div>
        </div>

        <form @submit.prevent="submitUpdate" v-else>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input
                    v-model="article.title"
                    type="text"
                    id="title"
                    class="form-control"
                    required
                />
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea
                    v-model="article.content"
                    id="content"
                    class="form-control"
                    rows="5"
                    required
                ></textarea>
            </div>

            <div class="d-flex w-100 justify-content-between">
                <button :disabled="isSubmiting" type="submit" class="btn btn-primary">Update Article</button>
                <router-link to="/" class="btn btn-secondary" :disabled="isSubmiting">Cancel</router-link>
            </div>

        </form>

        <!-- Success and error messages -->
        <div v-if="successMessage" class="alert alert-success mt-3">{{ successMessage }}</div>
        <div v-if="errorMessage" class="alert alert-danger mt-3">{{ errorMessage }}</div>
    </div>
</template>

<script setup>
import {useRoute, useRouter} from "vue-router";
import {onMounted, ref} from "vue";

const route = useRoute();
const router = useRouter();
const article = ref({
    title: '',
    content: '',
});
const isLoading = ref(true)
const isSubmiting = ref(false)
const successMessage = ref('');
const errorMessage = ref('');

onMounted(async () => {
    try {
        const response = await axios.get(`/articles/${route.params.id}/edit`);
        article.value = response.data;
        article.value.content = convertBrToNewline(article.value.content)
    } catch (error) {
        errorMessage.value = 'Failed to load article for editing';
    }
    isLoading.value = false
});

function convertBrToNewline(str) {
    return str.replace(/<br\s*\/?>/gi, '\n');
}

const submitUpdate = async () => {
    isSubmiting.value = true

    try {
        await axios.put(`/articles/${route.params.id}`, {
            title: article.value.title,
            content: article.value.content,
        });

        successMessage.value = 'Article updated successfully';
        errorMessage.value = ''; // Clear any previous error messages

        // Redirect to the article detail page after a successful update
        setTimeout(() => {
            router.push({ name: 'ArticleDetail', params: { id: route.params.id } })
        }, 1500);
    } catch (error) {
        errorMessage.value = 'Failed to update the article';
    }

    isSubmiting.value = false
}
</script>

<style scoped>
/* Skeleton loader styles */
.skeleton-loader {
    max-width: 600px;
    margin: 20px 0;
}

.skeleton {
    background-color: #e0e0e0;
    border-radius: 4px;
    margin-bottom: 10px;
    position: relative;
    overflow: hidden;
}

.skeleton:before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    height: 100%;
    width: 100%;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

.skeleton-title {
    width: 80%;
    height: 30px;
}

.skeleton-text {
    width: 100%;
    height: 20px;
}

/* Adjust the margin and form styles */
.container {
    max-width: 600px;
}

.alert {
    margin-top: 20px;
}
</style>
