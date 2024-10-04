<template>
    <div class="container mt-5">
        <!-- Check if article is loaded -->
        <div v-if="loading" class="alert alert-info">Loading article details...</div>

        <!-- Display article details -->
        <transition-group name="fade" tag="div">
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
                <div class="article-content" v-html="article.content"></div>

                <div class="upvote-section mt-4">
                    <button
                        v-if="!isUpvoted"
                        @click="upvoteArticle"
                        class="btn btn-outline-primary"
                        :disabled="isUpvoting"
                        title="upvote"
                    >
                        👍 ({{ upvoteCount }})
                    </button>
                    <button
                        v-if="isUpvoted"
                        @click="removeUpvote"
                        class="btn btn-primary"
                        :disabled="isUpvoting"
                        title="upvote"
                    >
                        👍 ({{ upvoteCount }})
                    </button>
                </div>

                <div class="comments mt-4">
                    <h3>Comments</h3>

                    <div v-if="isLoggedIn" class="mt-4 mb-4">
                        <h4>Add a Comment</h4>
                        <form @submit.prevent="submitComment">
                            <div class="mb-3">
                              <textarea
                                  v-model="newComment"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Write your comment"
                              ></textarea>
                            </div>
                            <!-- Error Message -->
                            <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>
                            <div class="d-flex w-100 justify-content-between">
                                <div></div>
                                <button type="submit" class="btn btn-primary">Submit Comment</button>
                            </div>

                        </form>
                    </div>


                    <div v-if="comments.length === 0" class="alert alert-info">No comments yet.</div>
                    <transition-group name="fade" tag="div">
                        <div v-for="comment in comments" :key="comment.id" class="comment-item mb-3">
                            <p><strong>{{ comment.user.name }}</strong>: {{ comment.content }}</p>
                        </div>
                    </transition-group>
                </div>

            </div>
        </transition-group>

        <router-link to="/" class="btn btn-secondary mt-3">Back to Articles</router-link>

    </div>
</template>

<script setup>
import {useRoute, useRouter} from "vue-router";
import {onMounted, ref} from "vue";
import Swal from 'sweetalert2'

const route = useRoute(); // Get route parameters
const router = useRouter(); // Get route parameters
const article = ref({}); // Reactive variable to store article details
const comments = ref([]);
const newComment = ref('');
const upvoteCount = ref(0);
const isUpvoted = ref(false);
const isUpvoting = ref(false);
const loading = ref(true); // Loading state
const errorMessage = ref(''); // Error message
const isOwnedByCurrentUser = ref(false);
const isLoggedIn = ref(window.Laravel.user != null);

onMounted(async () => {
    const articleId = route.params.id;
    axios(`/articles/${articleId}`)
        .then(response => {
            article.value = response.data.article
            upvoteCount.value = response.data.article.upvotes.length;
            isUpvoted.value = response.data.isUpvotedByUser;
            comments.value = response.data.article.comments.reverse()
            isOwnedByCurrentUser.value = response.data.isOwnedByCurrentUser;
        }).catch(error => {
        errorMessage.value = 'Failed to load article details.';
    }).finally(() => {
        loading.value = false;
    })
})

const submitComment = () => {
    errorMessage.value = ''

    axios.post(`/articles/${article.value.id}/comment`, {
        content: newComment.value,
    }).then(response => {
        comments.value.unshift(response.data);
        newComment.value = '';
    }).catch(error => {
        errorMessage.value = 'Failed to submit the comment';
    })
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

const upvoteArticle = async () => {
    isUpvoting.value = true
    try {
        await axios.post(`/articles/${article.value.id}/upvote`);
        upvoteCount.value++;
        isUpvoted.value = true;
    } catch (error) {
        errorMessage.value = 'Failed to upvote article';
    }
    isUpvoting.value = false
}

const removeUpvote = async () => {
    isUpvoting.value = true
    try {
        await axios.delete(`/articles/${article.value.id}/upvote`);
        upvoteCount.value--;
        isUpvoted.value = false;
    } catch (error) {
        errorMessage.value = 'Failed to remove upvote';
    }
    isUpvoting.value = false
}
</script>

<style scoped>
.article-content {
    white-space: pre-wrap; /* This ensures that new lines are preserved */
    margin-top: 20px;
}
.comments {
    margin-top: 30px;
}

.comment-item {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}

/* Transition animation for adding/removing comments */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
    transform: translateY(20px); /* Slide the comment up as it appears */
}
</style>
