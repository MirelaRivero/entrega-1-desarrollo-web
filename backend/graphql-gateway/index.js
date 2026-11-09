const express = require('express');
const { ApolloServer } = require('apollo-server-express');
const dotenv = require('dotenv');
const typeDefs = require('./schema');
const resolvers = require('./resolvers');

dotenv.config({ path: '../.env' });

const PORT = process.env.PORT_GRAPHQL || 9000;

async function startServer() {
    const app = express();

    const server = new ApolloServer({
        typeDefs,
        resolvers,
        introspection: true, // Habilitar introspección para desarrollo
        playground: true, // Habilitar GraphQL Playground para desarrollo
    });

    await server.start();
    server.applyMiddleware({ app, path: '/graphql' });

    app.listen(PORT, () => {
        console.log(`GraphQL Gateway ejecutandose en http://localhost:${PORT}/graphql`);
    });
}

startServer().catch(error => console.error('Error al iniciar el servidor GraphQL:', error));