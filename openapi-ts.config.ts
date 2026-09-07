import { defineConfig } from '@hey-api/openapi-ts';

export default defineConfig({
    input: 'contract/openapi/openapi.json',
    output: {
        path: 'resources/js/api',
        clean: true,
    },
});
