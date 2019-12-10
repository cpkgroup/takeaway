<template>
    <v-content>

        <h1>New Email</h1>
        <v-form
                ref="form"
                v-model="valid"
        >
            <v-text-field
                    v-model="formData.fromName"
                    :counter="50"
                    :rules="[rules.counter]"
                    label="From Name"
                    hint="Optional"
            ></v-text-field>
            <v-text-field
                    v-model="formData.fromEmail"
                    :rules="[rules.email]"
                    label="From Email"
                    hint="Optional"
            ></v-text-field>

            <v-combobox
                    v-model="formData.recipients"
                    hide-selected
                    hint="Press Enter to add an Email, Email or 'Name <Email>' are valid formats"
                    label="Recipients"
                    multiple
                    persistent-hint
                    small-chips
                    :rules="rules.multiEmailRules"
                    required
            >
            </v-combobox>

            <v-text-field
                    v-model="formData.subject"
                    :counter="50"
                    :rules="[rules.required, rules.counter]"
                    label="Subject"
                    required
            ></v-text-field>

            <v-textarea
                    v-model="formData.body"
                    :rules="[rules.required]"
                    label="Body"
                    required
            ></v-textarea>

            <v-textarea
                    v-model="formData.bodyTextPart"
                    label="Body Text Part"
                    hint="Optional"
            ></v-textarea>

            <v-radio-group v-model="formData.messageType" row>
                <v-radio label="Plain Text" value="0"></v-radio>
                <v-radio label="HTML" value="1"></v-radio>
                <v-radio label="Markdown" value="2"></v-radio>
            </v-radio-group>

            <v-btn
                    :disabled="!valid"
                    color="primary"
                    class="mr-4"
                    @click="createPost()"
            >
                Send
                <v-icon dark right>mdi-checkbox-marked-circle</v-icon>
            </v-btn>
        </v-form>

        <v-snackbar
                v-model="snackbar"
                top="true"
                vertical="true"
        >
            {{ snackbarText }}
            <v-btn
                    color="blue"
                    text
                    @click="snackbar = false"
            >
                Close
            </v-btn>
        </v-snackbar>

        <v-overlay
                opacity="0.7"
                :value="overlay"
                z-index="100"
        >
            <v-alert type="success">
                Email was send successfully
                <router-link to="/">
                    <v-btn x-small color="primary">
                        Show Logs
                    </v-btn>
                </router-link>
            </v-alert>
        </v-overlay>
    </v-content>

</template>

<script>
    export default {
        name: 'new',
        data() {
            return {
                valid: true,
                overlay: false,
                snackbar: false,
                snackbarText: '',
                // form data here
                formData: {
                    fromName: '',
                    fromEmail: '',
                    subject: '',
                    body: '',
                    bodyTextPart: '',
                    messageType: '1',
                    recipients: [],
                },
                // email regex
                emailRegex: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                emailAndNameRegex: /^[^<]+ *<(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))>$/,
                rules: {
                    required: value => !!value || 'Required',
                    counter: value => value.length <= 50 || 'Max 50 characters',
                    email: value => {
                        if (!value || value.length < 1) {
                            return true;
                        }
                        return this.emailRegex.test(value) || 'Invalid e-mail';
                    },
                    multiEmailRules: [
                        v => {
                            if (!v || v.length < 1) {
                                return 'E-mail is required';
                            }
                            if (v.length > 0) {
                                for (let i = 0; i < v.length; i++) {
                                    if (!this.emailRegex.test(v[i]) && !this.emailAndNameRegex.test(v[i])) {
                                        return 'E-mail must be valid';
                                    }
                                }
                            }
                            return true;
                        }
                    ],
                },
            }
        },
        methods: {
            createPost() {
                let recipientList = [];
                this.formData.recipients.map(function (recipient, key) {
                    let completeEmailRegex = /(.+) *<(.+@.+\..+)>/;
                    let match = completeEmailRegex.exec(recipient);
                    if (!match) {
                        recipientList.push({'email': recipient.trim()});
                    } else {
                        recipientList.push({'name': match[1].trim(), 'email': match[2].trim()});
                    }
                });
                this.snackbar = true;
                this.snackbarText = 'Sending Email...';
                this.$http.post('email/compose', {
                    fromName: this.formData.fromName,
                    fromEmail: this.formData.fromEmail,
                    subject: this.formData.subject,
                    body: this.formData.body,
                    bodyTextPart: this.formData.bodyTextPart,
                    messageType: parseInt(this.formData.messageType),
                    recipients: recipientList,
                }).then(response => {
                    if (response && response.status) {
                        this.snackbar = false;
                        this.snackbarText = '';
                        this.overlay = true;
                    }
                }).catch(error => {
                    let errorMessage = 'Error on sending email!';
                    if (error.response) {
                        errorMessage = JSON.stringify(error.response.data.errors);
                    }
                    this.snackbar = true;
                    this.snackbarText = errorMessage;
                })
            }
        }
    }
</script>
<style>

</style>