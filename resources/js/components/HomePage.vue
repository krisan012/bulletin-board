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
                    <div class="d-flex w-100 justify-content-between">
                        <router-link :to="{ name: 'ArticleDetail', params: { id: article.id } }">
                            <h5 class="mb-1">{{ article.title }}</h5>
                        </router-link>

                        <small>{{ article.created_at }}</small>
                    </div>
                    <div class="mb-1" v-html="htmlSnippet(article.content, 300)"></div>
                    <small>by: {{ article.user.name }}</small>
                </li>
            </ul>
        </div>

    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import DOMPurify from 'dompurify';

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

const htmlSnippet = (content, length) => {
    const strippedContent = DOMPurify.sanitize(content, { ALLOWED_TAGS: [] });
    return strippedContent.length > length ? strippedContent.substring(0, length) + '...' : strippedContent;
}
</script>

<style scoped>
.container {
    max-width: 800px;
}
</style>
