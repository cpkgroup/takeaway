<template>
    <v-content>
        <h1>Email Logs</h1>

        <v-simple-table fixed-header height="500px">
            <template v-slot:default>
                <thead>
                <tr>
                    <th class="text-left">Id</th>
                    <th class="text-left">From</th>
                    <th class="text-left">Subject</th>
                    <th class="text-left">Body</th>
                    <th class="text-left">Recipients</th>
                </tr>
                <tr>
                    <td colspan="5" :hidden="!loading">
                        <v-progress-linear
                                :active="loading"
                                color="red"
                                :indeterminate="true"
                        ></v-progress-linear>
                    </td>
                </tr>
                </thead>
                <tbody :hidden="loading">
                <tr v-for="email of emails" v-bind:key="email.id">
                    <td>{{ email.id }}</td>
                    <td><span v-if="email.from.email">{{ email.from.name }} <{{ email.from.email }}></span></td>
                    <td>{{ email.subject }}</td>
                    <td>{{ email.body }}</td>
                    <td>
                        <div v-for="recipient of email.recipients">
                            {{ recipient.name }} <{{ recipient.email }}>
                            <v-btn v-if="recipient.isSent" x-small color="success">Sent</v-btn>
                            <v-btn v-if="!recipient.isSent && !recipient.isPending" x-small color="error">Failed</v-btn>
                            <v-btn v-if="recipient.isPending" x-small color="warning">Pending</v-btn>
                            <v-btn v-if="recipient.sentAt" x-small color="primary">{{ recipient.sentAt }}</v-btn>
                            <v-btn v-if="recipient.provider" x-small color="secondary">{{ recipient.provider }}</v-btn>
                        </div>
                    </td>
                </tr>
                </tbody>
            </template>
        </v-simple-table>

        <v-pagination
                v-model="page"
                :length="numberOfPages"
                @input="fetchData"
        ></v-pagination>
    </v-content>
</template>

<script>
    export default {
        name: 'logs',
        data() {
            return {
                page: 1,
                numberOfPages: 0,
                emails: [],
                loading: true,
                color: 'red',
            }
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.loading = true;
                this.$http.get('email?page=' + this.page).then((response) => {
                    this.emails = response.data.items;
                    this.numberOfPages = response.data.numberOfPages;
                    this.loading = false;
                })
                    .catch((e) => {
                        console.error(e);
                    })
            }
        }
    }
</script>
