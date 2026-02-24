package com.joblens.joblens.entity;

import jakarta.persistence.Entity;
import jakarta.persistence.Id;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.Table;

@Entity
@Table(name = "wordcloud")
public class WordCloud {

    @Id
    private Long id;

    @ManyToOne
    @JoinColumn(name = "CompanyId", nullable = false)
    private Company company;

    private Double emotion;
    private String content;
    private String frequency;

    // Getters and setters
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Company getCompany() {
        return company;
    }

    public void setCompany(Company company) {
        this.company = company;
    }

    public Double getEmotion() {
        return emotion;
    }

    public void setEmotion(Double emotion) {
        this.emotion = emotion;
    }

    public String getContent() {
        return content;
    }

    public void setContent(String content) {
        this.content = content;
    }

    public String getFrequency() {
        return frequency;
    }

    public void setFrequency(String frequency) {
        this.frequency = frequency;
    }
}
