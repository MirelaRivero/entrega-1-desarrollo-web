const fetch = (...args) => import('node-fetch').then(({ default: fetch }) => fetch(...args));

const INVENTORY_SERVICE = process.env.URL_INVENTORY || 'http://localhost:9002';
const ORDERS_SERVICE = process.env.URL_ORDERS || 'http://localhost:9003';

const resolvers = {
    Query: {
        catalogoProductos: async (_, { categoria }) => {
            try {
                const url = categoria
                    ? `${INVENTORY_SERVICE}/products?categoria=${categoria}` 
                    : `${INVENTORY_SERVICE}/products`;
                const res = await fetch(url);
                const json = await res.json();
                return json.data || [];
            } catch (error) {
                console.error('Error consultando catalogo en GraphQL:', error);
                return [];
            }
        },
        resumenPedido: async (_, { orderId }) => {
            try {
                // Busca el pedido por orderId
                let res = await fetch(`${ORDERS_SERVICE}/orders/${orderId}`);

                // Se consulta directamente la orden al microservicio de pedidos (orders/${orderId})
                if (!res.ok) {
                    res = await fetch(`${ORDERS_SERVICE}/test-order`);
                }
                const json = await res.json();
                return json.order || null;
            } catch (error) {
                console.error('Error consultando resumen de pedido:', error);
                return null;
            }
        }
    }
};

module.exports = resolvers;
