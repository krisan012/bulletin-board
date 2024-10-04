<template>
    <div class="container mt-5">
        <h1 class="text-center">Bulletin Board</h1>
        <p class="text-center">A place where users can share articles, upvote, and comment.</p>
        <div class="text-center mt-4">
            <router-link to="/create-article" class="btn btn-primary">Create New Article</router-link>
        </div>

        <div v-if="loading" class="alert alert-info">Loading articles...</div>

        <div class="mt-5" v-else>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>Recent Articles</h1>
                <!-- Reload button -->
                <button @click="reload" class="btn btn-outline-primary">Reload Articles</button>
            </div>
            <ul class="list-group">
                <li class="list-group-item list-group-item-action flex-column align-items-start" v-for="article in articles" :key="article.id">
                    <div class="d-flex w-100 justify-content-between mb-3">
                        <router-link :to="{ name: 'ArticleDetail', params: { id: article.id } }">
                            <h5 class="mb-1">{{ article.title }}</h5>
                        </router-link>

                        <small>{{ article.created_at }}</small>
                    </div>
                    <div class="mb-1" v-html="htmlSnippet(article.content, 300)" style="overflow: hidden"></div>
                    <div class="d-flex w-100 justify-content-between mt-3">
                        <small>by: {{ article.user.name }}</small>

                        <button
                            v-if="isOwnedByUser(article)"
                            @click="confirmDelete"
                            class="btn btn-danger btn-sm"
                        >
                            Delete Article
                        </button>
                    </div>

                </li>
            </ul>
        </div>

    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import DOMPurify from 'dompurify';
import Swal from "sweetalert2";
import {useRoute} from "vue-router";

const route = useRoute();

const articles = ref([]);
const loading = ref(true);
const errorMessage = ref('');

onMounted(() => {
    fetchArticles()
})

const fetchArticles = async () => {
    loading.value = true
    await axios('/articles')
        .then(response => {
            articles.value = response.data
        }).catch(e => {
            errorMessage.value = 'Failed to load articles.';
        }).finally(() => {
            loading.value = false;
        })
}

const reload = () => {
    fetchArticles()
}

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
    }).then(async (result) => {
        if (result.isConfirmed) {
            await axios.delete(`/articles/${route.params.id}`)
                .then(response => {
                    Swal.fire('Deleted!', 'The article has been deleted.', 'success');
                }).catch(error => {
                    Swal.fire('Error', 'There was a problem deleting the article.', 'error');
                })
        }
    });
}

const htmlSnippet = (content, length) => {
    const strippedContent = DOMPurify.sanitize(content, { ALLOWED_TAGS: [] });
    return strippedContent.length > length ? strippedContent.substring(0, length) + '...' : strippedContent;
}

const isOwnedByUser = (article) => {
    return article.user_id == window.Laravel.user.id
}
</script>

<style scoped>
.container {
    max-width: 800px;
}
</style>
