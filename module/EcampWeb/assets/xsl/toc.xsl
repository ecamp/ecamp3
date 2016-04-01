<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:outline="http://wkhtmltopdf.org/outline"
                xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
                doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
                indent="yes" />
    <xsl:template match="outline:outline">
        <html>
            <head>
                <title>Table of Contents</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style>
                    body {
                        border: 0;
                        margin: 0;
                        padding: 0 20mm 0 20mm;
                    }
                    h1 {
                        text-align: center;
                        font-size: 40px;
                        font-family: arial;
                        padding-top: 60px;
                    }
                    table {
                        padding: 0;
                        margin: 1;
                        border-collapse: collapse;

                        width: 100%;
                        display: inline;
                    }
                    tr {
                        padding: 0;
                        margin: 0;
                    }
                    td {
                        padding: 0;
                        margin: 0;
                    }
                    span {
                        float: right;
                    }
                    li {
                        list-style: none;
                    }
                    ul td {
                        font-size: 30px;
                        font-family: arial;
                    }
                    ul ul td {
                        font-size: 90%;
                    }
                    ul ul ul td {
                        font-size: 80%;
                    }
                    ul {
                        padding-left: 0;
                    }
                    ul ul {
                        padding-left: 50px;
                    }
                    a {
                        color: black;
                        text-decoration: none;
                    }
                </style>
            </head>
            <body>
                <h1>Table of Contents</h1>
                <ul><xsl:apply-templates select="outline:item/outline:item"/></ul>
            </body>
        </html>
    </xsl:template>
    <xsl:template match="outline:item">


        <li>
            <xsl:if test="@title!=''">
                <table>
                    <colgroup>
                        <col width="0%" />
                        <col width="100%" />
                        <col width="20" />
                    </colgroup>
                    <tbody>
                        <tr>
                            <td style="white-space: nowrap;">
                                <a>
                                    <xsl:if test="@link">
                                        <xsl:attribute name="href"><xsl:value-of select="@link"/></xsl:attribute>
                                    </xsl:if>
                                    <xsl:if test="@backLink">
                                        <xsl:attribute name="name"><xsl:value-of select="@backLink"/></xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="@title" />
                                </a>
                            </td>
                            <td>
                                <div style="width: 100%;">
                                    <div style="margin: 0 5px 0 5px; border-bottom: 1px dashed #bbb; color: transparent; font-size: 75%;">
                                        _
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a>
                                    <xsl:if test="@link">
                                        <xsl:attribute name="href"><xsl:value-of select="@link"/></xsl:attribute>
                                    </xsl:if>
                                    <xsl:if test="@backLink">
                                        <xsl:attribute name="name"><xsl:value-of select="@backLink"/></xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="@page" />
                                </a>
                                <!--
                                <span> <xsl:value-of select="@page" /> </span>
                                -->
                            </td>
                        </tr>
                    </tbody>
                </table>
            </xsl:if>
            <ul>
                <xsl:comment>added to prevent self-closing tags in QtXmlPatterns</xsl:comment>
                <xsl:apply-templates select="outline:item"/>
            </ul>
        </li>



<!--
        <li>
            <xsl:if test="@title!=''">
                <table style="border: 1px solid red">
                    <colgroup>
                        <col width="0%" />
                        <col width="100%" />
                        <col width="0%" />
                    </colgroup>
                    <tbody>
                        <tr>
                            <td style="white-space: nowrap;">
                                <a>
                                    <xsl:if test="@link">
                                        <xsl:attribute name="href"><xsl:value-of select="@link"/></xsl:attribute>
                                    </xsl:if>
                                    <xsl:if test="@backLink">
                                        <xsl:attribute name="name"><xsl:value-of select="@backLink"/></xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="@title" />
                                </a>
                            </td>
                            <td>
                                <div style="width: 100%; margin: 5px; padding: 5px; border-bottom: 1px dotted #bbb;"></div>
                            </td>
                            <td>
                                <span> <xsl:value-of select="@page" /> </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </xsl:if>
            <ul>
                <xsl:comment>added to prevent self-closing tags in QtXmlPatterns</xsl:comment>
                <xsl:apply-templates select="outline:item"/>
            </ul>
        </li>
-->


    </xsl:template>
</xsl:stylesheet>