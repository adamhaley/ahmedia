import type { Metadata } from "next";
import "./globals.css";
import Navigation from "@/components/ui/Navigation";
import AnimatedLines from "@/components/animations/AnimatedLines";

export const metadata: Metadata = {
  title: "AH Media.ai - AI Agency",
  description: "Your AI transformation partner",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body className="antialiased">
        <Navigation />
        <AnimatedLines />
        <main className="pt-20">
          {children}
        </main>
      </body>
    </html>
  );
}
