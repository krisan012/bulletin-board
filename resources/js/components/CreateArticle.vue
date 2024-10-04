<template>
    <div class="container mt-5">
        <h1>Create a New Article</h1>
        <form @submit.prevent="submitArticle" class="mt-4">
            <!-- Title Field -->
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input
                    v-model="title"
                    type="text"
                    id="title"
                    class="form-control"
                    placeholder="Enter article title"
                />
            </div>

            <!-- Content Field -->
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea
                    v-model="content"
                    id="content"
                    class="form-control"
                    rows="5"
                    placeholder="Enter article content"
                ></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Success Message -->
        <div v-if="successMessage" class="alert alert-success mt-3">
            {{ successMessage }}
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="alert alert-danger mt-3">
            {{ errorMessage }}
        </div>
    </div>
</template>

<script setup>
import {computed, ref} from "vue";
    import {useRouter} from "vue-router";

    const title = ref()
    const content = ref()
    const successMessage = ref('');
    const errorMessage = ref('');
    const router = useRouter()

    const submitArticle = async () => {
        successMessage.value = ""
        errorMessage.value = ""

        await axios.post('/articles', {
            title: title.value,
            content: formattedContent.value
        }).then(response => {
            successMessage.value = response.data.message

            setTimeout(() => {
                router.push('/')
            }, 1000)

        }).catch(error => {
            if (error.response && error.response.data.errors) {
                errorMessage.value = Object.values(error.response.data.errors).join(', ');
            } else {
                errorMessage.value = 'An error occurred while submitting the article.';
            }
        })
    }

    const formattedContent = computed(() => content.value.replace(/\n/g, '<br>'));
</script>

<style scoped>

</style>
