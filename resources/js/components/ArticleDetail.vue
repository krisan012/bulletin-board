<template>
    <div class="container mt-5">
        <!-- Check if article is loaded -->
        <div v-if="loading" class="alert alert-info">Loading article details...</div>

        <!-- Display article details -->
        <div v-if="!loading">
            <div class="d-flex w-100 justify-content-between">
                <h1>{{ article.title }}</h1>

                <button
                    v-if="isOwnedByCurrentUser"
                    @click="confirmDelete"
                    class="btn btn-danger btn-sm"
                >
                    Delete Article
                </button>
            </div>

            <p><small class="text-muted">Published: {{ article.created_at }}</small></p>

            <!-- Use white-space: pre-wrap to preserve new lines in the content -->
            <div class="article-content" v-html="article.content">

            </div>

            <router-link to="/" class="btn btn-secondary mt-3">Back to Articles</router-link>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
    </div>
</template>

<script setup>
import {useRoute, useRouter} from "vue-router";
import {onMounted, ref} from "vue";
import Swal from 'sweetalert2'

const route = useRoute(); // Get route parameters
const router = useRouter(); // Get route parameters
const article = ref({}); // Reactive variable to store article details
const loading = ref(true); // Loading state
const errorMessage = ref(''); // Error message
const isOwnedByCurrentUser = ref(false);

onMounted(async () => {
    const articleId = route.params.id;
    axios(`/articles/${articleId}`)
        .then(response => {
            article.value = response.data.article
            isOwnedByCurrentUser.value = response.data.isOwnedByCurrentUser;
        }).catch(error => {
        errorMessage.value = 'Failed to load article details.';
    }).finally(() => {
        loading.value = false;
    })
})

const confirmDelete = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
    }).then((result) => {
        if (result.isConfirmed) {
            deleteArticle();
        }
    });
}

const deleteArticle = async () => {
    await axios.delete(`/articles/${route.params.id}`)
        .then(response => {
            Swal.fire('Deleted!', 'The article has been deleted.', 'success');
            setTimeout(() => {
                router.push('/')
            }, 1000)
        }).catch(error => {
            Swal.fire('Error', 'There was a problem deleting the article.', 'error');
        })
}
</script>

<style scoped>
.article-content {
    white-space: pre-wrap; /* This ensures that new lines are preserved */
    margin-top: 20px;
}
</style>
