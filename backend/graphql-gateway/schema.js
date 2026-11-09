const { gql } = require('apollo-server-express');

const typeDefs = gql`
    type Producto {
        id: Int!
        nombre: String!
        categoria: String!
        precio: Int!
        descripcion: String!
        imagen: String!
    }
    
    type ItemPedido {
        productId: Int!
        nombre: String!
        precio: Int!
        cantidad: Int!
    }

    type ClienteInfo {
        nombre: String!
        email: String!
        rut: String!
        telefono: String!
    }

    type DespachoInfo {
        direccion: String!
        region: String!
        comuna: String! 
    }

    type PedidoResumen {
        id: ID!
        orderId: String!
        cliente: ClienteInfo!
        despacho: DespachoInfo!
        items: [ItemPedido!]!
        subtotal: Int!
        costoEnvio: Int!
        totalFinal: Int!
        estadoPedido: String!
        creadoEn: String
    }

    type Query {
        # Agregacion, obtener pedidos con detalles combinados
        resumenPedido(orderId: String!): PedidoResumen

        # Consulta rapida al inventario
        catalogoProductos(categoria: String): [Producto!]!
    }
`;

module.exports = typeDefs;
